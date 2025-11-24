<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{   
    protected $table = 'audits';

    protected $fillable = [
        'asset_id',
        'location',
        'next_audit_date',
        'notes',
        'attachment_path',
        'attachment_name',
        'audited_at',
        'audited_by',
    ];

    protected $casts = [
        'audited_at' => 'datetime',
        'next_audit_date' => 'date',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    // Helper method to get attachment URL
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_path 
            ? asset('storage/' . $this->attachment_path) 
            : null;
    }
}
