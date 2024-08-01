<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'presence_status_id',
        'company_id',
        'date',
        'check_in',
        'check_out',
        'attachment',
        'is_approved',
        'description',
    ];

    protected $with = [
        'presenceStatus',
    ];

    protected $appends = [
        'attachment_url',
    ];

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? url($this->attachment) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function presenceStatus()
    {
        return $this->belongsTo(PresenceStatus::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
