<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicField extends Model
{
    protected $table = 'dynamic_fields';

    protected $fillable = [
        'field',
        'value'
    ];
}
