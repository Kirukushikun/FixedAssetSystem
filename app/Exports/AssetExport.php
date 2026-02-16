<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class AssetExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Return the collection of data to export
     */
    public function collection()
    {
        $query = Asset::where('is_deleted', false);

        // Apply filters
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

        // Age filter (based on acquisition_date)
        if (!empty($this->filters['age_min']) || !empty($this->filters['age_max'])) {
            $query->whereNotNull('acquisition_date');

            if (!empty($this->filters['age_min'])) {
                $maxDate = Carbon::now()->subYears($this->filters['age_min']);
                $query->where('acquisition_date', '<=', $maxDate);
            }

            if (!empty($this->filters['age_max'])) {
                $minDate = Carbon::now()->subYears($this->filters['age_max']);
                $query->where('acquisition_date', '>=', $minDate);
            }
        }

        return $query->select(
            'ref_id',
            'category_type',
            'category',
            'sub_category',
            'brand',
            'model',
            'status',
            'condition',
            'acquisition_date',
            'item_cost',
            'depreciated_value',
            'usable_life',
            'assigned_name',
            'assigned_id',
            'farm',
            'department',
            'remarks'
        )->get();
    }

    /**
     * Define the column headings
     */
    public function headings(): array
    {
        return [
            'Reference ID',
            'Category Type',
            'Category',
            'Sub Category',
            'Brand',
            'Model',
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
            'Remarks'
        ];
    }
}