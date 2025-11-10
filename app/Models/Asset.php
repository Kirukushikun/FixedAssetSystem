<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
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

        'technical_data'
    ];

    protected $cast = [
        'is_deleted' => 'boolean',
        'is_archived' => 'boolean',
        'acquisition_date' => 'datetime',
        'technical_data' => 'array'
    ];
}
