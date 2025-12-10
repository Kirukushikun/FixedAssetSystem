<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'subcategories';

    protected $fillable = ['name', 'category_id', 'category_type'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
