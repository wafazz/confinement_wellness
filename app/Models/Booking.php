<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'client_id', 'client_name', 'client_phone',
        'client_email', 'client_address', 'state', 'district',
        'service_type', 'preferred_date', 'preferred_time',
        'preferred_therapist_id', 'status', 'source', 'notes',
        'admin_notes', 'reviewed_by', 'reviewed_at',
        'referral_code', 'referred_by_type', 'referred_by_id',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function preferredTherapist()
    {
        return $this->belongsTo(User::class, 'preferred_therapist_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function serviceJob()
    {
        return $this->hasOne(ServiceJob::class);
    }
}
