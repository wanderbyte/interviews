<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'photo_path',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
