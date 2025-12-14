<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_name'
    ];

    /**
     * Category has many materials
     */
    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}
