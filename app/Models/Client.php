<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function ($client) {
            if (!$client->referral_code) {
                do {
                    $code = 'CREF-' . strtoupper(\Illuminate\Support\Str::random(5));
                } while (static::where('referral_code', $code)->exists());
                $client->referral_code = $code;
            }
        });
    }

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'address', 'state', 'district', 'status',
        'referral_code', 'reward_points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function serviceJobs()
    {
        return $this->hasMany(ServiceJob::class);
    }

    public function rewardPoints()
    {
        return $this->hasMany(ClientRewardPoint::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
