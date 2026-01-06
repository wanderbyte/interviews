<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_name',
        'standard_id',
        'gender',
        'year',
        'photo',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }
}
