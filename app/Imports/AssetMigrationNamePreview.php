<?php

namespace App\Imports;

use App\Models\SubCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithReadFilter;
use App\Imports\Filters\ColumnRangeFilter;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Reads a migration file without importing it, collecting each distinct
 * "Assigned To" name with how many assets (and how many Issued) carry it.
 * Uses the same row rules as AssetMigrationImport so the counts line up.
 */
class AssetMigrationNamePreview implements ToCollection, WithHeadingRow, WithCalculatedFormulas, WithColumnLimit, WithReadFilter
{
    /** @var array<string, array{total: int, issued: int}> */
    public array $names = [];

    public int $rows = 0;

    /** @var array<string, array{count: int, category: string}> sub-category as written in the file, not in the system */
    public array $unknownSubCategories = [];

    private array $knownSubCategories;

    public function __construct()
    {
        $this->knownSubCategories = SubCategory::pluck('name')
            ->mapWithKeys(fn ($name) => [AssetMigrationImport::key($name) => true])
            ->all();
    }

    public function endColumn(): string
    {
        return AssetMigrationImport::LAST_COLUMN;
    }

    public function readFilter(): IReadFilter
    {
        return new ColumnRangeFilter(AssetMigrationImport::LAST_COLUMN);
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $r) {
            $subCategory = trim((string) ($r['sub_category'] ?? ''));
            $brand       = trim((string) ($r['brand'] ?? ''));
            $model       = trim((string) ($r['model'] ?? ''));

            if (($subCategory === '' && $brand === '' && $model === '') || trim((string) ($r['farm'] ?? '')) === '') {
                continue;
            }

            $this->rows++;

            if (!isset($this->knownSubCategories[AssetMigrationImport::key($subCategory)])) {
                $this->unknownSubCategories[$subCategory] ??= [
                    'count'    => 0,
                    'category' => trim((string) ($r['category'] ?? '')),
                ];
                $this->unknownSubCategories[$subCategory]['count']++;
            }

            $name = trim((string) ($r['assigned_to'] ?? ''));
            if ($name === '') {
                continue;
            }

            $this->names[$name] ??= ['total' => 0, 'issued' => 0];
            $this->names[$name]['total']++;
            if (strcasecmp(trim((string) ($r['status'] ?? '')), 'Issued') === 0) {
                $this->names[$name]['issued']++;
            }
        }
    }
}
