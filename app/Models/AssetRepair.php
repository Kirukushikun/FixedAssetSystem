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
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
