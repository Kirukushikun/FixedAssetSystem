<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name', 'icon', 'code'];

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}
