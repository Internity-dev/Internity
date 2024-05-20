<?php

namespace App\Models;

use App\Services\FCMService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vacancy_id',
        'status',
        'message',
    ];

    protected static function booted()
    {
        static::updated(function ($appliance) {
            if ($appliance->status == 'accepted') {
                $notif = Notification::create([
                    'user_id' => $appliance->user_id,
                    'title' => 'Lamaran PKL Kamu Diterima!',
                    'body' => 'Selamat! Lamaran PKL kamu untuk lowongan ' . $appliance->vacancy->name . ' di ' . $appliance->vacancy->company->name . ' telah diterima. Silakan hubungi perusahaan untuk lebih lanjut.',
                ]);

                FCMService::send($appliance->user->fcm_token, [$notif->title, $notif->body]);
            } elseif ($appliance->status == 'rejected') {
                $notif = Notification::create([
                    'user_id' => $appliance->user_id,
                    'title' => 'Yahh, lamaran PKL kamu ditolak.',
                    'body' => 'Maaf, lamaran PKL kamu untuk lowongan ' . $appliance->vacancy->name . ' di ' . $appliance->vacancy->company->name . ' telah ditolak. Jangan menyerah, coba lagi untuk lowongan lainnya!',
                ]);

                FCMService::send($appliance->user->fcm_token, [$notif->title, $notif->body]);
            } elseif ($appliance->status == 'processed') {
                $notif = Notification::create([
                    'user_id' => $appliance->user_id,
                    'title' => 'Lamaran PKL Kamu Sedang Diproses',
                    'body' => 'Lamaran PKL kamu untuk lowongan ' . $appliance->vacancy->name . ' di ' . $appliance->vacancy->company->name . ' sedang diproses oleh pihak sekolah. Harap sabar menunggu kabar selanjutnya.',
                ]);

                FCMService::send($appliance->user->fcm_token, [$notif->title, $notif->body]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeVacancy($query, $vacancy)
    {
        return $query->where('vacancy_id', $vacancy);
    }

    public function scopeUser($query, $user)
    {
        return $query->where('user_id', $user);
    }
}
