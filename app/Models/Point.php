<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'user_id', 'service_job_id', 'points', 'month',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceJob()
    {
        return $this->belongsTo(ServiceJob::class);
    }
}
