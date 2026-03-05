<?php

namespace App\Notifications;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobAssigned extends Notification
{
    use Queueable;

    public function __construct(public ServiceJob $job) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Job Assigned',
            'message' => 'You have been assigned job ' . $this->job->job_code . ' (' . $this->job->service_type . ') for ' . $this->job->client_name . '.',
            'job_id' => $this->job->id,
            'job_code' => $this->job->job_code,
            'type' => 'job_assigned',
        ];
    }
}
