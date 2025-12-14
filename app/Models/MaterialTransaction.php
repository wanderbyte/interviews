<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTransaction extends Model
{
    protected $fillable = [
        'material_id',
        'quantity',
        'transaction_date',
    ];

    /**
     * Transaction belongs to material
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
