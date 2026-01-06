<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'school_name',
        'school_address',
        'state_id',
        'district_id',
        'city_id',
        'establishment_date',
        'contact_number',
        'login_id',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function photos()
    {
        return $this->hasMany(SchoolPhoto::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
