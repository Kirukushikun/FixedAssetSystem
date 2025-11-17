<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{   
    protected $table = 'history';

    protected $fillable = [
        'asset_id',
        'assignee_id',
        'assignee_name',
        'status',
        'condition',
        'farm',
        'department',
        'action',
    ];
}
