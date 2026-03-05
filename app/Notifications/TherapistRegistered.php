<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TherapistRegistered extends Notification
{
    use Queueable;

    public function __construct(public User $therapist, public User $leader) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Therapist Registration',
            'message' => $this->leader->name . ' has registered ' . $this->therapist->name . ' as a new therapist. Pending your approval.',
            'type' => 'therapist_registered',
            'user_id' => $this->therapist->id,
            'user_name' => $this->therapist->name,
        ];
    }
}
