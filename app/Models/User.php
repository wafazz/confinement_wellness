<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (in_array($user->role, ['leader', 'therapist']) && !$user->referral_code) {
                do {
                    $code = 'REF-' . strtoupper(\Illuminate\Support\Str::random(5));
                } while (static::where('referral_code', $code)->exists());
                $user->referral_code = $code;
            }
        });
    }

    protected $fillable = [
        'name', 'email', 'phone', 'ic_number', 'password', 'role',
        'leader_id', 'state', 'district', 'kkm_cert_no',
        'bank_name', 'bank_account', 'status', 'profile_photo',
        'referral_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function therapists()
    {
        return $this->hasMany(User::class, 'leader_id');
    }

    public function assignedJobs()
    {
        return $this->hasMany(ServiceJob::class, 'assigned_to');
    }

    public function createdJobs()
    {
        return $this->hasMany(ServiceJob::class, 'assigned_by');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function sopMaterials()
    {
        return $this->hasMany(SopMaterial::class, 'uploaded_by');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
