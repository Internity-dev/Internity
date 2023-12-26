<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Searchable;

class Vacancy extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'category',
        'description',
        'status',
        'slots',
        'skills',
        'company_id',
    ];

    protected $with = [
        'company',
    ];

    protected $appends = [
        'applied',
        'pending_appliances',
        'processed_appliances',
        'in_processed',
        'in_pending'
    ];

    public function getAppliedAttribute()
    {
        return $this->appliances()->where('status', '!=', 'canceled')->count();
    }

    public function getInProcessedAttribute()
    {
        return $this->appliances()->where('status', 'processed')->where('user_id', auth()->id())->count() > 0 ? true : false;
    }

    public function getInPendingAttribute()
    {
        return $this->appliances()->where('status', 'pending')->where('user_id', auth()->id())->count() > 0 ? true : false;
    }

    public function getPendingAppliancesAttribute()
    {
        return $this->appliances()->where('status', 'pending')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->first();
    }

    public function getProcessedAppliancesAttribute()
    {
        return $this->appliances()->where('status', 'processed')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->first();
    }

    // #[SearchUsingFullText(['skills'])]
    // public function toSearchableArray()
    // {
    //     return [
    //         'name' => $this->name,
    //         'category' => $this->category,
    //         'description' => $this->description,
    //         'status' => $this->status,
    //         'slots' => $this->slots,
    //         'skills' => $this->skills,
    //         'company_id' => $this->company_id,
    //     ];
    // }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_vacancy', 'vacancy_id', 'department_id');
    }

    public function appliances()
    {
        return $this->hasMany(Appliance::class);
    }

    public function scopeCompany($query, $company)
    {
        return $query->where('company_id', $company);
    }
}
