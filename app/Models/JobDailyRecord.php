<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobDailyRecord extends Model
{
    protected $fillable = [
        'service_job_id', 'day_number', 'date', 'status',
        'therapist_check_in_at', 'therapist_check_in_lat', 'therapist_check_in_lng',
        'therapist_check_out_at', 'therapist_check_out_lat', 'therapist_check_out_lng',
        'leader_check_in_at', 'leader_check_in_lat', 'leader_check_in_lng',
        'leader_check_out_at', 'leader_check_out_lat', 'leader_check_out_lng',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'therapist_check_in_at' => 'datetime',
            'therapist_check_out_at' => 'datetime',
            'leader_check_in_at' => 'datetime',
            'leader_check_out_at' => 'datetime',
            'therapist_check_in_lat' => 'decimal:7',
            'therapist_check_in_lng' => 'decimal:7',
            'therapist_check_out_lat' => 'decimal:7',
            'therapist_check_out_lng' => 'decimal:7',
            'leader_check_in_lat' => 'decimal:7',
            'leader_check_in_lng' => 'decimal:7',
            'leader_check_out_lat' => 'decimal:7',
            'leader_check_out_lng' => 'decimal:7',
        ];
    }

    public function serviceJob()
    {
        return $this->belongsTo(ServiceJob::class);
    }
}
