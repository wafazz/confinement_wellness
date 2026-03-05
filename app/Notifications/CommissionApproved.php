<?php

namespace App\Notifications;

use App\Models\Commission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommissionApproved extends Notification
{
    use Queueable;

    public function __construct(public Commission $commission) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Commission Approved',
            'message' => 'Your ' . $this->commission->type . ' commission of RM ' . number_format($this->commission->amount, 2) . ' for ' . $this->commission->month . ' has been approved.',
            'commission_id' => $this->commission->id,
            'type' => 'commission_approved',
        ];
    }
}
