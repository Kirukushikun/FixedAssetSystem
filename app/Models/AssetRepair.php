<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRepair extends Model
{
    protected $fillable = [
        'asset_id',
        'date',
        'type',
        'cost',
        'notes',
        'source',
        'service_report_path',
        'service_report_name',
        'service_report_remarks',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
