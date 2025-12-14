<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialTransaction extends Model
{
    use SoftDeletes;
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
