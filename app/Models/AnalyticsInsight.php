<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsInsight extends Model
{
    protected $fillable = [
        'farm_filter',
        'insights',
        'metrics_snapshot',
        'generated_by_user_id',
        'generated_by_name',
    ];

    protected $casts = [
        'metrics_snapshot' => 'array',
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }
}
