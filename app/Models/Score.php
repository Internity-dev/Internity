<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'score',
        'type',
    ];

    protected $appends = [
        'score_predicate',
    ];

    public function getScorePredicateAttribute()
    {
        return ScorePredicate::where('min', '<=', $this->score)
            ->where('max', '>=', $this->score)
            ->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
