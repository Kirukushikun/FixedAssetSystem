<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetExport implements FromCollection, WithHeadings
{
    /**
     * Return the collection of data to export
     */
    public function collection()
    {
        return Asset::where('is_deleted', false)
        ->select(
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
