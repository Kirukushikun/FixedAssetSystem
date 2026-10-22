<?php

namespace App\Imports;

use App\Jobs\GenerateAssetQrCode;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\SubCategory;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Imports\Filters\ColumnRangeFilter;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use Maatwebsite\Excel\Row;

/**
 * Migration import — reads the human-friendly migration template.
 * Category and Category Type are taken from the Sub Category (matched case-insensitively);
 * the file's Category column is ignored. Sub-categories the system doesn't have must be
 * mapped (or skipped) on the review screen.
 * Assigned To is linked to an employee only through matches a person confirmed on the
 * review screen; only Issued assets are linked unless $linkAvailable is set. Unlinked
 * names are kept as text in assigned_name.
 * Status and Condition default to Available / Good when blank.
 * Always creates new assets — never updates existing ones.
 * Blank Brand / Model are stored as "N/A".
 * Duplicate check: by Serial Number only; rows without one are always created.
 */
class AssetMigrationImport implements
    OnEachRow,
    WithHeadingRow,
    WithCalculatedFormulas,
    WithColumnLimit,
    WithReadFilter
{
    // Template columns are A–O. Some department files have formatting dragged out to
    // ~16,000 columns; reading past O builds millions of empty cells and exhausts memory.
    // No WithChunkReading: its own read filter replaces this one, and files are small.
    public const LAST_COLUMN = 'O';

    public int $createdCount = 0;
    public int $skippedCount = 0;
    public int $linkedCount = 0;

    /** @var array<string, SubCategory> lowercased sub-category name => sub-category (with category) */
    private array $subCategories;

    /** @var array<string, ?SubCategory> sub-category as written in the file => mapped sub-category, null = skip */
    private array $subCategoryMappings = [];

    /**
     * @param array<string, Employee> $matches              name as written in the file => confirmed employee
     * @param array<string, ?int>     $subCategoryMappings  sub-category as written in the file => subcategories.id, null = skip those rows
     */
    public function __construct(
        private array $matches = [],
        private bool $linkAvailable = false,
        array $subCategoryMappings = [],
    ) {
        $all = SubCategory::with('category:id,code')->get();

        $this->subCategories = $all
            ->mapWithKeys(fn ($sub) => [self::key($sub->name) => $sub])
            ->all();

        foreach ($subCategoryMappings as $fileName => $id) {
            $this->subCategoryMappings[(string) $fileName] = $id === null ? null : $all->firstWhere('id', $id);
        }
    }

    public static function key(string $name): string
    {
        return mb_strtolower(trim($name));
    }

    public function onRow(Row $row): void
    {
        $r = $row->toArray();

        $subCategory = trim((string) ($r['sub_category'] ?? ''));
        $brand       = trim((string) ($r['brand'] ?? ''));
        $model       = trim((string) ($r['model'] ?? ''));

        // Skip fully blank rows
        if ($subCategory === '' && $brand === '' && $model === '') {
            return;
        }

        // Skip rows missing farm
        if (empty(trim((string) ($r['farm'] ?? '')))) {
            $this->skippedCount++;
            return;
        }

        $serialNo = trim((string) ($r['serial_number'] ?? ''));

        // Only a serial number identifies a duplicate — identical items (e.g. many plastic chairs) are separate assets
        if ($serialNo !== '' && Asset::where('serial_no', $serialNo)->exists()) {
            $this->skippedCount++;
            return;
        }

        if (array_key_exists($subCategory, $this->subCategoryMappings)) {
            $sub = $this->subCategoryMappings[$subCategory];
            if ($sub === null) {
                $this->skippedCount++;
                return;
            }
        } else {
            $sub = $this->subCategories[self::key($subCategory)] ?? null;
        }

        // The review screen requires a mapping for every unknown sub-category; this guards direct use.
        if (!$sub || !$sub->category) {
            throw new \RuntimeException("Sub category \"{$subCategory}\" does not exist in the system.");
        }

        $status     = trim((string) ($r['status'] ?? '')) ?: 'Available';
        $assignedTo = trim((string) ($r['assigned_to'] ?? ''));
        $employee   = $this->matches[$assignedTo] ?? null;

        if ($employee && !$this->linkAvailable && strcasecmp($status, 'Issued') !== 0) {
            $employee = null;
        }

        if ($employee) {
            $this->linkedCount++;
        }

        $asset = Asset::create([
            'category_type'    => $sub->category_type,
            'category'         => $sub->category->code,
            'sub_category'     => $sub->name,
            'brand'            => $brand ?: 'N/A',
            'model'            => $model ?: 'N/A',
            'serial_no'        => $serialNo ?: null,
            'status'           => $status,
            'condition'        => trim((string) ($r['condition'] ?? 'Good'))    ?: 'Good',
            'acquisition_date' => $this->parseDate($r['acquisition_date'] ?? null),
            'item_cost'        => $this->parseCost($r['item_cost'] ?? null),
            'assigned_name'    => $employee?->employee_name ?? ($assignedTo ?: null),
            'assigned_id'      => $employee?->id,
            'farm'             => strtoupper(trim((string) ($r['farm'] ?? ''))),
            'department'       => trim((string) ($r['department'] ?? '')) ?: null,
            'location'         => trim((string) ($r['location'] ?? ''))   ?: null,
            'remarks'          => trim((string) ($r['remarks'] ?? ''))    ?: null,
        ]);

        // afterCommit: the import runs in a transaction; a rolled-back asset gets no QR job
        GenerateAssetQrCode::dispatch($asset)->afterCommit();

        $this->createdCount++;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                    ->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseCost(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;
        $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '', (string) $value));
        return $clean === '' ? null : $clean;
    }

    public function endColumn(): string
    {
        return self::LAST_COLUMN;
    }

    public function readFilter(): IReadFilter
    {
        return new ColumnRangeFilter(self::LAST_COLUMN);
    }
}
