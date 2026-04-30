<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Flag;
use App\Models\Asset;
use App\Models\GeneratedForm;
use App\Models\AssetSmeReview;

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

    public function generatedForms()
    {
        return $this->hasMany(GeneratedForm::class)->latest();
    }

    public function smeReviews()
    {
        return $this->hasMany(AssetSmeReview::class)->latest();
    }
}