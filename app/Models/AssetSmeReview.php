<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetSmeReview extends Model
{
    protected $table = 'asset_sme_reviews';

    protected $fillable = [
        'employee_id',
        'asset_id',
        'reviewed_by_user_id',
        'reviewed_by_name',
        'condition_note',
        'remarks',
        'recommended_flag',
        'created_flag_id',
        'flagged_employee',
    ];

    protected $casts = [
        'flagged_employee' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function createdFlag()
    {
        return $this->belongsTo(Flag::class, 'created_flag_id');
    }
}
