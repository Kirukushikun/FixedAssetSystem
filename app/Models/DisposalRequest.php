<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisposalRequest extends Model
{
    protected $table = 'disposal_requests';

    protected $fillable = [
        'asset_id',
        'requested_by_user_id',
        'requested_by_name',
        'requester_farm',
        'requester_department',
        'reason',
        'attachment_path',
        'attachment_name',
        'status',
        'vp_approved_by_user_id',
        'vp_approved_by_name',
        'vp_approved_at',
        'accounting_disposed_by_user_id',
        'accounting_disposed_by_name',
        'disposed_at',
    ];

    protected $casts = [
        'vp_approved_at' => 'datetime',
        'disposed_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
}
