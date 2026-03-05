<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\CommissionRule;
use App\Models\ServiceJob;
use App\Models\User;
use App\Notifications\BookingReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create()
    {
        $services = CommissionRule::where('status', 'active')->get();
        $states = User::where('role', 'therapist')
            ->where('status', 'active')
            ->whereNotNull('state')
            ->distinct()
            ->pluck('state')
            ->sort()
            ->values();

        return view('public.booking', compact('services', 'states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => 'required|string|max:100',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'required|string',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'preferred_therapist_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'referral_code' => 'nullable|string|max:20',
        ]);

        $rule = CommissionRule::where('service_type', $validated['service_type'])
            ->where('status', 'active')
            ->first();

        $clientId = null;
        $source = 'guest';

        if (Auth::guard('client')->check()) {
            $clientId = Auth::guard('client')->id();
            $source = 'registered';
        }

        // Resolve referral code
        $referralCode = $validated['referral_code'] ?? null;
        $referredByType = null;
        $referredById = null;

        if ($referralCode) {
            // Check users (staff) first
            $referrer = User::where('referral_code', $referralCode)->first();
            if ($referrer) {
                $referredByType = 'user';
                $referredById = $referrer->id;
            } else {
                // Check clients
                $referrerClient = Client::where('referral_code', $referralCode)->first();
                if ($referrerClient) {
                    // Self-referral guard
                    if ($clientId && $referrerClient->id === $clientId) {
                        return back()->withInput()->withErrors(['referral_code' => 'You cannot use your own referral code.']);
                    }
                    $referredByType = 'client';
                    $referredById = $referrerClient->id;
                } else {
                    return back()->withInput()->withErrors(['referral_code' => 'Invalid referral code.']);
                }
            }
        }

        $bookingCode = $this->generateBookingCode();

        $bookingData = [
            'booking_code' => $bookingCode,
            'client_id' => $clientId,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'client_email' => $validated['client_email'] ?? null,
            'client_address' => $validated['client_address'],
            'state' => $validated['state'],
            'district' => $validated['district'],
            'service_type' => $validated['service_type'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'preferred_therapist_id' => $validated['preferred_therapist_id'] ?? null,
            'source' => $source,
            'notes' => $validated['notes'] ?? null,
            'referral_code' => $referralCode,
            'referred_by_type' => $referredByType,
            'referred_by_id' => $referredById,
        ];

        if ($rule && !$rule->requires_review) {
            $bookingData['status'] = 'approved';
            $booking = Booking::create($bookingData);
            $this->autoCreateServiceJob($booking);
        } else {
            $bookingData['status'] = 'pending_review';
            $booking = Booking::create($bookingData);
        }

        $this->notifyAdmins($booking);

        return redirect()->route('public.booking.confirmation', $booking->booking_code);
    }

    public function confirmation($code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();
        return view('public.booking-confirmation', compact('booking'));
    }

    public function therapistsByState(Request $request)
    {
        $therapists = User::where('role', 'therapist')
            ->where('status', 'active')
            ->where('state', $request->state)
            ->select('id', 'name')
            ->get();

        return response()->json($therapists);
    }

    private function generateBookingCode(): string
    {
        $date = now()->format('Ymd');
        $count = Booking::whereDate('created_at', today())->count() + 1;
        return 'BK-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    private function autoCreateServiceJob(Booking $booking): void
    {
        $leader = User::where('role', 'leader')
            ->where('state', $booking->state)
            ->where('status', 'active')
            ->first();

        $therapist = null;
        if ($booking->preferred_therapist_id) {
            $therapist = User::find($booking->preferred_therapist_id);
        }

        if (!$therapist && $leader) {
            $therapist = User::where('role', 'therapist')
                ->where('leader_id', $leader->id)
                ->where('status', 'active')
                ->first();
        }

        if (!$leader || !$therapist) {
            $booking->update(['status' => 'pending_review']);
            return;
        }

        $jobCode = 'JOB-' . $booking->preferred_date->format('Ymd') . '-' .
            str_pad(ServiceJob::whereDate('job_date', $booking->preferred_date)->count() + 1, 3, '0', STR_PAD_LEFT);

        ServiceJob::create([
            'job_code' => $jobCode,
            'client_name' => $booking->client_name,
            'client_phone' => $booking->client_phone,
            'client_address' => $booking->client_address,
            'state' => $booking->state,
            'district' => $booking->district,
            'service_type' => $booking->service_type,
            'job_date' => $booking->preferred_date,
            'job_time' => $booking->preferred_time,
            'assigned_by' => $leader->id,
            'assigned_to' => $therapist->id,
            'status' => 'pending',
            'notes' => $booking->notes,
            'client_id' => $booking->client_id,
            'booking_id' => $booking->id,
        ]);

        $booking->update(['status' => 'converted']);
    }

    private function notifyAdmins(Booking $booking): void
    {
        $admins = User::where('role', 'hq')->get();
        foreach ($admins as $admin) {
            $admin->notify(new BookingReceived($booking));
        }

        $stateLeader = User::where('role', 'leader')
            ->where('state', $booking->state)
            ->where('status', 'active')
            ->first();
        if ($stateLeader) {
            $stateLeader->notify(new BookingReceived($booking));
        }
    }
}
