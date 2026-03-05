<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'client_id', 'service_job_id', 'user_id',
        'rating', 'comment', 'status',
        'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime'];
    }

    public function client()     { return $this->belongsTo(Client::class); }
    public function serviceJob() { return $this->belongsTo(ServiceJob::class); }
    public function user()       { return $this->belongsTo(User::class); }
    public function approver()   { return $this->belongsTo(User::class, 'approved_by'); }
}
