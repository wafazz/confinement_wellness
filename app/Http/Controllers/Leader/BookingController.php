<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CommissionRule;
use App\Models\ServiceJob;
use App\Models\User;
use App\Notifications\JobAssigned;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $leaderState = auth()->user()->state;

        if ($request->ajax()) {
            $bookings = Booking::with(['client', 'preferredTherapist'])
                ->where('state', $leaderState);

            if ($request->filled('filter_status')) {
                $bookings->where('status', $request->filter_status);
            }

            return DataTables::of($bookings)
                ->addColumn('status_badge', function ($row) {
                    $color = match ($row->status) {
                        'pending_review' => 'warning',
                        'approved' => 'info',
                        'converted' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('source_badge', function ($row) {
                    $color = $row->source === 'registered' ? 'primary' : 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->source) . '</span>';
                })
                ->editColumn('preferred_date', fn($row) => $row->preferred_date->format('d M Y'))
                ->addColumn('action', function ($row) {
                    $viewUrl = route('leader.bookings.show', $row->id);
                    $btns = '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a> ';
                    if ($row->status === 'pending_review') {
                        $approveUrl = route('leader.bookings.approve', $row->id);
                        $btns .= '<form action="' . $approveUrl . '" method="POST" class="d-inline">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm(\'Approve this booking?\')"><i class="fas fa-check"></i></button>'
                            . '</form> ';
                    }
                    if ($row->status === 'approved') {
                        $convertUrl = route('leader.bookings.convert-form', $row->id);
                        $btns .= '<a href="' . $convertUrl . '" class="btn btn-sm btn-primary" title="Convert to Job"><i class="fas fa-exchange-alt"></i></a> ';
                    }
                    return $btns;
                })
                ->rawColumns(['status_badge', 'source_badge', 'action'])
                ->make(true);
        }

        return view('leader.bookings.index');
    }

    public function show(Booking $booking)
    {
        $this->authorizeBooking($booking);
        $booking->load(['client', 'preferredTherapist', 'reviewer', 'serviceJob.assignee']);
        return view('leader.bookings.show', compact('booking'));
    }

    public function approve(Booking $booking)
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'pending_review') {
            return back()->with('error', 'Only pending bookings can be approved.');
        }

        $booking->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Booking approved. You can now convert it to a job.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'pending_review') {
            return back()->with('error', 'Only pending bookings can be rejected.');
        }

        $request->validate(['admin_notes' => 'required|string|max:500']);

        $booking->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Booking rejected.');
    }

    public function convertForm(Booking $booking)
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'approved') {
            return back()->with('error', 'Only approved bookings can be converted.');
        }

        $therapists = User::where('role', 'therapist')
            ->where('leader_id', auth()->id())
            ->where('status', 'active')
            ->get();
        $serviceTypes = CommissionRule::where('status', 'active')->pluck('service_type');

        return view('leader.bookings.convert', compact('booking', 'therapists', 'serviceTypes'));
    }

    public function convert(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'approved') {
            return back()->with('error', 'Only approved bookings can be converted.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'job_date' => 'required|date',
            'job_time' => 'required',
        ]);

        $therapist = User::findOrFail($validated['assigned_to']);
        if ($therapist->leader_id !== auth()->id()) {
            abort(403, 'This therapist is not in your team.');
        }

        $jobCode = 'JOB-' . date('Ymd', strtotime($validated['job_date'])) . '-' .
            str_pad(ServiceJob::whereDate('job_date', $validated['job_date'])->count() + 1, 3, '0', STR_PAD_LEFT);

        $job = ServiceJob::create([
            'job_code' => $jobCode,
            'client_name' => $booking->client_name,
            'client_phone' => $booking->client_phone,
            'client_address' => $booking->client_address,
            'state' => $booking->state,
            'district' => $booking->district,
            'service_type' => $booking->service_type,
            'job_date' => $validated['job_date'],
            'job_time' => $validated['job_time'],
            'assigned_by' => auth()->id(),
            'assigned_to' => $validated['assigned_to'],
            'status' => 'pending',
            'notes' => $booking->notes,
            'client_id' => $booking->client_id,
            'booking_id' => $booking->id,
        ]);

        $booking->update(['status' => 'converted']);

        $therapist->notify(new JobAssigned($job));

        return redirect()->route('leader.bookings.index')->with('success', 'Booking converted to job ' . $jobCode . ' successfully.');
    }

    private function authorizeBooking(Booking $booking)
    {
        if ($booking->state !== auth()->user()->state) {
            abort(403, 'Unauthorized access to this booking.');
        }
    }
}
