<?php

namespace App\Models;

use App\Models\MaterialTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'material_name',
        'opening_balance',
        'current_balance'
    ];

    /**
     * Material belongs to category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(MaterialTransaction::class, 'material_id');
    }
}
