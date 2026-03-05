<?php

namespace App\Notifications;

use App\Models\ServiceJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobCompleted extends Notification
{
    use Queueable;

    public function __construct(public ServiceJob $job) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $therapistName = $this->job->assignee->name ?? 'A therapist';

        return [
            'title' => 'Job Completed',
            'message' => $therapistName . ' completed job ' . $this->job->job_code . ' (' . $this->job->service_type . ').',
            'job_id' => $this->job->id,
            'job_code' => $this->job->job_code,
            'type' => 'job_completed',
        ];
    }
}
