<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedForm extends Model
{
    protected $table = 'generated_forms';

    protected $fillable = [
        'employee_id',
        'asset_id',
        'form_type',
        'title',
        'generated_by_user_id',
        'snapshot',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
