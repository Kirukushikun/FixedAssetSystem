<?php

namespace App\Exports;

use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Full system backup export — includes all DB fields (ref_id, category code, assigned_id).
 * Use this for system-to-system migration or creating a restorable backup.
 * The exported file can be re-imported via System Import.
 */
class AssetExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Asset::where('is_deleted', false);

        if (!empty($this->filters['category_type'])) {
            $query->where('category_type', $this->filters['category_type']);
        }
        if (!empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
        }
        if (!empty($this->filters['sub_category'])) {
            $query->where('sub_category', 'like', '%' . $this->filters['sub_category'] . '%');
        }
        if (!empty($this->filters['farm'])) {
            $query->where('farm', $this->filters['farm']);
        }
        if (!empty($this->filters['department'])) {
            $query->where('department', $this->filters['department']);
        }

        if (!empty($this->filters['age_min']) || !empty($this->filters['age_max'])) {
            $query->whereNotNull('acquisition_date');
            if (!empty($this->filters['age_min'])) {
                $query->where('acquisition_date', '<=', Carbon::now()->subYears((int) $this->filters['age_min']));
            }
            if (!empty($this->filters['age_max'])) {
                $query->where('acquisition_date', '>=', Carbon::now()->subYears((int) $this->filters['age_max']));
            }
        }

        return $query->get()->map(fn ($a) => [
            $a->ref_id,
            $a->category_type,
            $a->category,          // stored code (e.g. 'COMP'), not human name
            $a->sub_category,
            $a->brand,
            $a->model,
            $a->serial_no,
            $a->status,
            $a->condition,
            $a->acquisition_date?->format('Y-m-d'),
            $a->item_cost,
            $a->depreciated_value,
            $a->usable_life,
            $a->assigned_name,
            $a->assigned_id,       // FK — preserved for system restore
            $a->farm,
            $a->department,
            $a->location,
            $a->remarks,
        ]);
    }

    public function headings(): array
    {
        return [
            'Reference ID',
            'Category Type',
            'Category',
            'Sub Category',
            'Brand',
            'Model',
            'Serial Number',
            'Status',
            'Condition',
            'Acquisition Date',
            'Item Cost',
            'Depreciated Value',
            'Usable Life',
            'Assigned Name',
            'Assigned ID',
            'Farm',
            'Department',
            'Location',
            'Remarks',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '1A535C']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CBF0EE']],
            ],
        ];
    }
}
