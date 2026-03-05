<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Models\JobDailyRecord;
use App\Models\Commission;
use App\Models\Point;
use App\Models\Client;
use App\Models\ClientRewardPoint;
use App\Models\CommissionRule;
use App\Models\JobUpdate;
use App\Models\User;
use App\Notifications\JobCompleted;
use Illuminate\Http\Request;

class ServiceJobController extends Controller
{
    public function index()
    {
        $jobs = ServiceJob::with('assigner')
            ->where('assigned_to', auth()->id())
            ->orderByRaw("FIELD(status, 'checked_in', 'accepted', 'pending', 'completed', 'cancelled')")
            ->orderBy('job_date', 'asc')
            ->orderBy('job_time', 'asc')
            ->paginate(15);

        return view('therapist.jobs.index', compact('jobs'));
    }

    public function show(ServiceJob $job)
    {
        $this->authorizeJob($job);
        $job->load(['assigner', 'commissions.user', 'points.user', 'dailyRecords', 'updates.user', 'review.client']);
        return view('therapist.jobs.show', compact('job'));
    }

    public function accept(ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->status !== 'pending') {
            return back()->with('error', 'Only pending jobs can be accepted.');
        }

        $job->update(['status' => 'accepted']);

        return back()->with('success', 'Job accepted successfully.');
    }

    // Wellness single-day check-in (existing flow)
    public function checkIn(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->status !== 'accepted') {
            return back()->with('error', 'You can only check in to accepted jobs.');
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $job->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
            'checked_in_lat' => $request->latitude,
            'checked_in_lng' => $request->longitude,
        ]);

        return back()->with('success', 'Checked in successfully. GPS location recorded.');
    }

    // Wellness single-day check-out (existing flow)
    public function checkOut(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->status !== 'checked_in') {
            return back()->with('error', 'You can only check out from checked-in jobs.');
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $job->update([
            'status' => 'completed',
            'checked_out_at' => now(),
            'checked_out_lat' => $request->latitude,
            'checked_out_lng' => $request->longitude,
            'completed_at' => now(),
        ]);

        $this->calculateCommissionAndPoints($job);

        if ($job->assigned_by) {
            $leader = User::find($job->assigned_by);
            if ($leader) {
                $leader->notify(new JobCompleted($job));
            }
        }

        return back()->with('success', 'Checked out successfully. Job completed! Commission and points awarded.');
    }

    // Multi-day daily check-in
    public function dailyCheckIn(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if (!$job->isMultiDay()) {
            return back()->with('error', 'Daily check-in is only for Stay In / Daily Visit jobs.');
        }

        if (!in_array($job->status, ['accepted', 'checked_in'])) {
            return back()->with('error', 'Job must be accepted first.');
        }

        $request->validate([
            'day_id' => 'required|exists:job_daily_records,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $record = JobDailyRecord::where('id', $request->day_id)
            ->where('service_job_id', $job->id)
            ->firstOrFail();

        if ($record->therapist_check_in_at) {
            return back()->with('error', 'Already checked in for Day ' . $record->day_number . '.');
        }

        $record->update([
            'status' => 'checked_in',
            'therapist_check_in_at' => now(),
            'therapist_check_in_lat' => $request->latitude,
            'therapist_check_in_lng' => $request->longitude,
        ]);

        // Update main job status
        if ($job->status === 'accepted') {
            $job->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
                'checked_in_lat' => $request->latitude,
                'checked_in_lng' => $request->longitude,
            ]);
        }

        $job->update(['current_day' => $record->day_number]);

        return back()->with('success', 'Checked in for Day ' . $record->day_number . '.');
    }

    // Multi-day daily check-out
    public function dailyCheckOut(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if (!$job->isMultiDay()) {
            return back()->with('error', 'Daily check-out is only for Stay In / Daily Visit jobs.');
        }

        $request->validate([
            'day_id' => 'required|exists:job_daily_records,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $record = JobDailyRecord::where('id', $request->day_id)
            ->where('service_job_id', $job->id)
            ->firstOrFail();

        if (!$record->therapist_check_in_at) {
            return back()->with('error', 'Must check in first for Day ' . $record->day_number . '.');
        }

        if ($record->therapist_check_out_at) {
            return back()->with('error', 'Already checked out for Day ' . $record->day_number . '.');
        }

        $record->update([
            'status' => 'completed',
            'therapist_check_out_at' => now(),
            'therapist_check_out_lat' => $request->latitude,
            'therapist_check_out_lng' => $request->longitude,
        ]);

        // Check if this is the last day
        $allComplete = $job->dailyRecords()->where('status', '!=', 'completed')->count() === 0;

        if ($allComplete) {
            $job->update([
                'status' => 'completed',
                'checked_out_at' => now(),
                'checked_out_lat' => $request->latitude,
                'checked_out_lng' => $request->longitude,
                'completed_at' => now(),
            ]);

            $this->calculateCommissionAndPoints($job);

            if ($job->assigned_by) {
                $leader = User::find($job->assigned_by);
                if ($leader) {
                    $leader->notify(new JobCompleted($job));
                }
            }

            return back()->with('success', 'Day ' . $record->day_number . ' completed. All days done — Job completed! Commission and points awarded.');
        }

        return back()->with('success', 'Checked out for Day ' . $record->day_number . '. ' . ($job->work_days - $record->day_number) . ' days remaining.');
    }

    public function addNotes(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        $request->validate(['notes' => 'required|string|max:1000']);
        $job->update(['notes' => $request->notes]);

        return back()->with('success', 'Notes updated successfully.');
    }

    public function postUpdate(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->status !== 'checked_in') {
            return back()->with('error', 'Updates can only be posted while checked in.');
        }

        $request->validate([
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('job-updates', 'public');
        }

        JobUpdate::create([
            'service_job_id' => $job->id,
            'user_id' => auth()->id(),
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Update posted successfully.');
    }

    private function authorizeJob(ServiceJob $job)
    {
        if ($job->assigned_to !== auth()->id()) {
            abort(403, 'Unauthorized access to this job.');
        }
    }

    private function calculateCommissionAndPoints(ServiceJob $job)
    {
        $rule = CommissionRule::where('service_type', $job->service_type)
            ->where('status', 'active')
            ->first();

        if (!$rule) {
            return;
        }

        $month = $job->completed_at->format('Y-m');
        $price = (float) $rule->price;

        // Therapist direct commission
        $therapistAmt = $rule->getTherapistAmount($price);
        Commission::create([
            'user_id' => $job->assigned_to,
            'service_job_id' => $job->id,
            'type' => 'direct',
            'amount' => $therapistAmt,
            'month' => $month,
            'status' => 'pending',
        ]);

        // Leader override commission — skip if leader did the job themselves
        if ($job->assigned_by && $job->assigned_by !== $job->assigned_to) {
            $leaderAmt = $rule->getLeaderAmount($price);
            Commission::create([
                'user_id' => $job->assigned_by,
                'service_job_id' => $job->id,
                'type' => 'override',
                'amount' => $leaderAmt,
                'month' => $month,
                'status' => 'pending',
            ]);
        }

        // Affiliate commission (if job came from a booking with referral)
        $job->load('booking');
        if ($job->booking && $job->booking->referred_by_id) {
            $affiliateAmt = $rule->getAffiliateAmount($price);
            if ($affiliateAmt > 0) {
                // Staff referral → affiliate commission
                if ($job->booking->referred_by_type === 'user') {
                    Commission::create([
                        'user_id' => $job->booking->referred_by_id,
                        'service_job_id' => $job->id,
                        'type' => 'affiliate',
                        'amount' => $affiliateAmt,
                        'month' => $month,
                        'status' => 'pending',
                    ]);
                }
            }
            // Client referral → reward points
            if ($job->booking->referred_by_type === 'client' && $rule->customer_referral_points > 0) {
                $client = Client::find($job->booking->referred_by_id);
                if ($client) {
                    ClientRewardPoint::create([
                        'client_id' => $client->id,
                        'booking_id' => $job->booking_id,
                        'points' => $rule->customer_referral_points,
                        'type' => 'earned',
                        'description' => 'Referral reward for booking ' . $job->booking->booking_code,
                    ]);
                    $client->increment('reward_points', $rule->customer_referral_points);
                }
            }
        }

        Point::create([
            'user_id' => $job->assigned_to,
            'service_job_id' => $job->id,
            'points' => $rule->points_per_job,
            'month' => $month,
        ]);
    }
}
