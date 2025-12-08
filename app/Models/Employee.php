<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Flag;
use App\Models\Asset;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'is_deleted',
        'employee_id',
        'employee_name',
        'position',
        'farm',
        'department',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function flags()
    {
        return $this->hasMany(Flag::class, 'target_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'assigned_id')
                    ->where('is_deleted', false);
    }
}