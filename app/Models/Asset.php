<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'snipe_id',
        'is_deleted',
        'is_archived',

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

        'technical_data',

        'assigned_name',
        'assigned_id',
        'farm',
        'department',

        'qr_code',
        'attachment',
        'attachment_name',

        'remarks'
    ];

    protected $cast = [
        'is_deleted' => 'boolean',
        'is_archived' => 'boolean',
        'acquisition_date' => 'datetime',
        'technical_data' => 'array'
    ];

    /**
     * Ensure ref_id is set to a unique, incremental value BEFORE creation
     * if it wasn't provided (this prevents DB errors when the column
     * is non-nullable and ensures seeders/creators get a predictable id).
     */
    protected static function booted()
    {
        static::creating(function ($asset) {
            if (empty($asset->ref_id)) {
                try {
                    $status = DB::select("SHOW TABLE STATUS LIKE 'assets'");
                    $nextId = $status[0]->Auto_increment ?? (DB::table('assets')->max('id') + 1);
                } catch (\Exception $e) {
                    $nextId = DB::table('assets')->max('id') + 1;
                }

                $year = now()->year;
                $asset->ref_id = 'FA-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });

        // Keep a fallback: after created, ensure ref_id is present (rare)
        static::created(function ($asset) {
            if (empty($asset->ref_id)) {
                $year = now()->year;
                $asset->ref_id = 'FA-' . $year . '-' . str_pad($asset->id, 4, '0', STR_PAD_LEFT);
                $asset->saveQuietly();
            }
        });
    }

    // Add relationship
    public function audits()
    {
        return $this->hasMany(Audit::class);
    }

    public function latestAudit()
    {
        return $this->hasOne(Audit::class)->latestOfMany('audited_at');
    }

    // In your Asset model
    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_id');
    }
}
