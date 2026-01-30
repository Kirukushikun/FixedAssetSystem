<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Audit;
use App\Models\Employee;
use App\Models\History;

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
        'location',

        'qr_code',
        'attachment',
        'attachment_name',

        'remarks'
    ];

    protected $casts = [  // Fixed: should be 'casts' not 'cast'
        'is_deleted' => 'boolean',
        'is_archived' => 'boolean',
        'acquisition_date' => 'datetime',
        'technical_data' => 'array'
    ];

    /**
     * Generate the next incremental ref_id based on the last one
     */
    protected static function booted()
    {
        static::creating(function ($asset) {
            if (empty($asset->ref_id)) {
                $year = now()->year;
                
                // Get the last ref_id for this year
                $lastRefId = DB::table('assets')
                    ->where('ref_id', 'LIKE', "FA-{$year}-%")
                    ->orderByRaw('CAST(SUBSTRING(ref_id, 9) AS UNSIGNED) DESC')
                    ->value('ref_id');
                
                if ($lastRefId) {
                    // Extract the number from FA-2024-0001 format
                    $lastNumber = (int) substr($lastRefId, 8);
                    $nextNumber = $lastNumber + 1;
                } else {
                    // First asset of the year
                    $nextNumber = 1;
                }
                
                $asset->ref_id = 'FA-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function audits()
    {
        return $this->hasMany(Audit::class);
    }

    public function latestAudit()
    {
        return $this->hasOne(Audit::class)->latestOfMany('audited_at');
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_id');
    }
    /**
     * Get the history records for the asset
     */
    public function history()
    {
        return $this->hasMany(History::class, 'asset_id');
    }

    /**
     * Get the category details
     */
    public function categoryDetails()
    {
        return $this->belongsTo(Category::class, 'category', 'code');
    }
}