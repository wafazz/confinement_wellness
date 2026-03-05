<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::guard('client')->user()
            ->bookings()
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('client.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        if ($booking->client_id !== Auth::guard('client')->id()) {
            abort(403);
        }

        $booking->load(['preferredTherapist', 'serviceJob.assignee', 'serviceJob.review']);

        return view('client.bookings.show', compact('booking'));
    }
}
