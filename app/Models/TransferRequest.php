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
        'approved_by',
        'approved_by_name',
        'approved_at',
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
