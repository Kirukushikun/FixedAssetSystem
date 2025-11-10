<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'ref_id',
        'category_type',
        'category'
    ];
}
