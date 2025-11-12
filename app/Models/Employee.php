<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'assigned_assets',
        'flags',
    ];

    protected $cast = [
        'is_deleted' => 'boolean',
        'flags' => 'array'
    ];
}
