<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'address',
        'phone',
        'gender',
        'date_of_birth',
        'status',
        'skills',
        'resume',
        'password_by_admin',
        'last_login',
        'last_login_ip'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
    ];

    protected $appends = [
        'avatar_url',
        'resume_url',
        'in_internship',
        'in_processed',
        'in_pending',
    ];

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function getResumeUrlAttribute()
    {
        return $this->resume ? asset('storage/' . $this->resume) : null;
    }

    public function getInInternshipAttribute()
    {
        return $this->internDates()->where('finished', 0)->count() > 0 ? true : false;
    }

    public function getInPendingAttribute()
    {
        return $this->appliances()->where('status', 'pending')->count() > 0 ? true : false;
    }

    public function getInProcessedAttribute()
    {
        return $this->appliances()->where('status', 'processed')->count() > 0 ? true : false;
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_user', 'user_id', 'school_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user', 'user_id', 'company_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function internDates()
    {
        return $this->hasMany(InternDate::class);
    }

    public function appliances()
    {
        return $this->hasMany(Appliance::class);
    }

    public function monitors()
    {
        return $this->hasMany(Monitor::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%$search%")
                    ->where('email', 'like', "%$search%");
    }

    public function scopeManager($query, $school_id)
    {
        return $query->whereHas('schools', function ($query) use ($school_id) {
            $query->where('school_id', $school_id);
        });
    }

    public function scopeTeacher($query, $department_id)
    {
        return $query->whereRelation('departments', 'id', $department_id)
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'student');
                    })
                    ->orWhereHas('companies', function ($query) use ($department_id) {
                        $query->where('department_id', $department_id);
                    });
    }

    public function scopeMentor($query, $company_id)
    {
        return $query->whereRelation('companies', 'id', $company_id)
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'student');
                    });
    }
}
