<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class AssetImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{   
    public $createdCount = 0;
    public $updatedCount = 0;

    public function model(array $row)
    {
        // Check if record already exists
        $existing = Asset::where('ref_id', $row['reference_id'])
            ->where('brand', $row['brand'])
            ->where('model', $row['model'])
            ->first();

        if ($existing) {
            return null; // skip duplicates
        }

        // Create new record
        $model = Asset::create([
            'ref_id'         => $row['ref_id'],
            'category_type'  => $row['category_type'],
            'category'       => $row['category'],
            'sub_category'   => $row['sub_category'],
            'brand'          => $row['brand'],
            'model'          => $row['model'],
            'status'         => $row['status'],
            'condition'      => $row['condition'],
            'acquisition_date' => $row['acquisition_date'],
            'item_cost'      => $row['item_cost'],
            'depreciated_value' => $row['depreciated_value'],
            'usable_life'    => $row['usable_life'],
            'assigned_name'  => $row['assigned_name'],
            'assigned_id'    => $row['assigned_id'],
            'farm'           => $row['farm'],
            'department'     => $row['department'],
            'remarks'        => $row['remarks'],
        ]);

        $this->createdCount++;

        return $model;
    }
}
