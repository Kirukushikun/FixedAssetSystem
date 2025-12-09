<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class AssetImport implements 
    OnEachRow,
    WithHeadingRow,
    WithCalculatedFormulas,
    WithChunkReading
{
    public $createdCount = 0;
    public $updatedCount = 0;

    public function onRow(Row $row)
    {
        $r = $row->toArray();

        // Check duplicate
        $exists = Asset::where('ref_id', $r['reference_id'])
            ->where('brand', $r['brand'])
            ->where('model', $r['model'])
            ->exists();

        if ($exists) {
            return;
        }

        // Create
        Asset::create([
            'ref_id'            => $r['reference_id'],
            'category_type'     => $r['category_type'],
            'category'          => $r['category'],
            'sub_category'      => $r['sub_category'],
            'brand'             => $r['brand'],
            'model'             => $r['model'],
            'status'            => $r['status'],
            'condition'         => $r['condition'],
            'acquisition_date'  => $r['acquisition_date'],
            'item_cost'         => $r['item_cost'],
            'depreciated_value' => $r['depreciated_value'],
            'usable_life'       => $r['usable_life'],
            'assigned_name'     => $r['assigned_name'],
            'assigned_id'       => $r['assigned_id'],
            'farm'              => $r['farm'],
            'department'        => $r['department'],
            'remarks'           => $r['remarks'],
        ]);

        $this->createdCount++;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
