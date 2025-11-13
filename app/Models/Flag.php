<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flag extends Model
{
    protected $table = 'flags';

    protected $fillable = [
        'flag_type',
        'asset',
    ];
}
