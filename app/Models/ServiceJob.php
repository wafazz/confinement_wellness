<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceJob extends Model
{
    protected $fillable = [
        'job_code', 'client_name', 'client_phone', 'client_address',
        'state', 'district', 'service_type', 'service_category', 'work_days', 'current_day',
        'job_date', 'job_end_date', 'job_time',
        'assigned_by', 'assigned_to', 'status', 'notes',
        'checked_in_at', 'checked_in_lat', 'checked_in_lng',
        'checked_out_at', 'checked_out_lat', 'checked_out_lng',
        'completed_at', 'client_id', 'booking_id',
    ];

    protected function casts(): array
    {
        return [
            'job_date' => 'date',
            'job_end_date' => 'date',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'completed_at' => 'datetime',
            'checked_in_lat' => 'decimal:7',
            'checked_in_lng' => 'decimal:7',
            'checked_out_lat' => 'decimal:7',
            'checked_out_lng' => 'decimal:7',
        ];
    }

    public function isMultiDay(): bool
    {
        return in_array($this->service_category, ['stay_in', 'daily_visit']);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'service_job_id');
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'service_job_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function dailyRecords()
    {
        return $this->hasMany(JobDailyRecord::class)->orderBy('day_number');
    }

    public function updates()
    {
        return $this->hasMany(JobUpdate::class)->orderBy('created_at', 'desc');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
