<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'status',
        'school_id',
        'study_program',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'department_user', 'department_id', 'user_id');
    }

    public function news()
    {
        return $this->morphMany(News::class, 'newsable');
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeInactive()
    {
        return $this->where('status', 0);
    }

    public function scopeSearch($search)
    {
        return $this->where('name', $search);
    }
}
