<?php

namespace App\Models;

use App\Services\FCMService;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Searchable;

class News extends Model
{
    use HasFactory;
    use Sluggable;

    protected $fillable = [
        'title',
        'content',
        'status',
        'image',
        'user_id',
        'newsable_id',
        'newsable_type',
        'image_url'
    ];

    protected $appends = [
        'image_url',
    ];

    protected static function booted()
    {
        static::created(function ($news) {
            $news = Notification::create([
                'user_id' => null,
                'title' => 'Ada Berita Baru!',
                'body' => 'Ada berita baru yang ditambahkan oleh ' . $news->user->name . '. Segera cek di sesi berita!',
            ]);

            $users = User::whereNotNull('fcm_token')->get();

            foreach ($users as $user) {
                FCMService::send($user->fcm_token, [$news->title, $news->body]);
            }
        });
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? url($this->image) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newsable()
    {
        return $this->morphTo();
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }
}
