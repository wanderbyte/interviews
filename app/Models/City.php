<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'city_name'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
