<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

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
