<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{

    protected $fillable = [
        'category_id',
        'material_name',
        'opening_balance'
    ];

    /**
     * Material belongs to category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Material has many transactions
     */
    public function transactions()
    {
        return $this->hasMany(MaterialTransaction::class);
    }

    /**
     * Calculate current balance
     */
    public function getCurrentBalanceAttribute()
    {
        return $this->opening_balance + $this->transactions()->sum('quantity');
    }
}
