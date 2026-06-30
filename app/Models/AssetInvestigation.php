<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetInvestigation extends Model
{
    protected $fillable = [
        'asset_id',
        'status',
        'resolution',
        'notes',
        'opened_by_user_id',
        'opened_by_name',
        'resolved_by_user_id',
        'resolved_by_name',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
