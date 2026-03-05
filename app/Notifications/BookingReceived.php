<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingReceived extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Booking Received',
            'message' => $this->booking->client_name . ' submitted a booking (' . $this->booking->booking_code . ') for ' . $this->booking->service_type . ' in ' . $this->booking->state . '.',
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'type' => 'booking_received',
        ];
    }
}
