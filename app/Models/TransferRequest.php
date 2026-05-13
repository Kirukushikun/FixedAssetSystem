<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRequest extends Model
{
    protected $fillable = [
        'asset_id',
        'requested_by',
        'requested_by_name',
        'requested_employee_id',
        'requested_employee_name',
        'reason',
        'status',
        'division_head_approved_by_user_id',
        'division_head_approved_by_name',
        'division_head_approved_at',
        'approved_by',
        'approved_by_name',
        'approved_at',
        'is_external',
        'external_farm',
        'external_department',
    ];

    protected $casts = [
        'division_head_approved_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_external' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requestedEmployee()
    {
        return $this->belongsTo(Employee::class, 'requested_employee_id');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
