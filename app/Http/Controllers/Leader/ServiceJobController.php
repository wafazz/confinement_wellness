<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Models\JobDailyRecord;
use App\Models\Commission;
use App\Models\Point;
use App\Models\Client;
use App\Models\ClientRewardPoint;
use App\Models\User;
use App\Models\CommissionRule;
use App\Models\JobUpdate;
use App\Notifications\JobAssigned;
use App\Notifications\JobCompleted;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ServiceJobController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jobs = ServiceJob::with('assignee')
                ->where(function ($q) {
                    $q->where('assigned_by', auth()->id())
                      ->orWhere('assigned_to', auth()->id());
                });

            if ($request->filled('filter_status')) {
                $jobs->where('status', $request->filter_status);
            }

            return DataTables::of($jobs)
                ->addColumn('assigned_to_name', fn($row) => $row->assignee->name ?? '-')
                ->addColumn('category_badge', function ($row) {
                    $map = ['stay_in' => ['Stay In', 'warning'], 'daily_visit' => ['Daily Visit', 'info'], 'wellness' => ['Wellness', 'primary']];
                    $cat = $map[$row->service_category] ?? ['Wellness', 'primary'];
                    return '<span class="badge bg-' . $cat[1] . '">' . $cat[0] . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $color = match ($row->status) {
                        'pending' => 'secondary',
                        'accepted' => 'primary',
                        'checked_in' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('leader.jobs.show', $row->id);
                    $editUrl = route('leader.jobs.edit', $row->id);
                    $btns = '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a> ';
                    if (!in_array($row->status, ['completed', 'cancelled'])) {
                        $btns .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a> ';
                    }
                    if ($row->status !== 'completed' && $row->status !== 'cancelled') {
                        $cancelUrl = route('leader.jobs.cancel', $row->id);
                        $btns .= '<form action="' . $cancelUrl . '" method="POST" class="d-inline">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="btn btn-sm btn-danger" title="Cancel" onclick="return confirm(\'Cancel this job?\')"><i class="fas fa-times"></i></button>'
                            . '</form>';
                    }
                    return $btns;
                })
                ->editColumn('job_date', function ($row) {
                    $date = $row->job_date->format('d M Y');
                    if ($row->job_end_date) {
                        $date .= ' — ' . $row->job_end_date->format('d M Y');
                    }
                    return $date;
                })
                ->rawColumns(['category_badge', 'status_badge', 'action'])
                ->make(true);
        }

        return view('leader.jobs.index');
    }

    public function create()
    {
        $therapists = User::where('role', 'therapist')
            ->where('leader_id', auth()->id())
            ->where('status', 'active')
            ->get();
        $commissionRules = CommissionRule::where('status', 'active')->get();

        return view('leader.jobs.create', compact('therapists', 'commissionRules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_address' => 'required|string',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'service_type' => 'required|string|max:100',
            'job_date' => 'required|date',
            'job_end_date' => 'nullable|date|after_or_equal:job_date',
            'job_time' => 'required',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Allow self-assignment or team therapist
        $assignee = User::findOrFail($validated['assigned_to']);
        if ($assignee->id !== auth()->id() && $assignee->leader_id !== auth()->id()) {
            abort(403, 'This user is not in your team.');
        }

        $rule = CommissionRule::where('service_type', $validated['service_type'])->where('status', 'active')->first();

        $validated['service_category'] = $rule->service_category ?? 'wellness';
        $validated['work_days'] = $rule->work_days ?? null;
        $validated['assigned_by'] = auth()->id();
        $validated['job_code'] = $this->generateJobCode($validated['job_date']);
        $validated['status'] = 'pending';

        if ($validated['service_category'] !== 'wellness' && $validated['work_days']) {
            $validated['job_end_date'] = Carbon::parse($validated['job_date'])->addDays($validated['work_days'] - 1)->format('Y-m-d');
        } else {
            $validated['job_end_date'] = null;
            $validated['work_days'] = null;
        }

        $job = ServiceJob::create($validated);

        if ($job->isMultiDay() && $job->work_days) {
            $this->createDailyRecords($job);
        }

        // Notify assignee (skip if self-assigned)
        if ($assignee->id !== auth()->id()) {
            $assignee->notify(new JobAssigned($job));
        }

        return redirect()->route('leader.jobs.index')->with('success', 'Job created and assigned successfully.');
    }

    public function show(ServiceJob $job)
    {
        $this->authorizeJob($job);
        $job->load(['assigner', 'assignee', 'commissions.user', 'points.user', 'dailyRecords', 'updates.user', 'review.client']);
        $isAssignee = $job->assigned_to === auth()->id();
        return view('leader.jobs.show', compact('job', 'isAssignee'));
    }

    public function edit(ServiceJob $job)
    {
        $this->authorizeJob($job);

        if (in_array($job->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Completed or cancelled jobs cannot be edited.');
        }

        $therapists = User::where('role', 'therapist')
            ->where('leader_id', auth()->id())
            ->where('status', 'active')
            ->get();
        $commissionRules = CommissionRule::where('status', 'active')->get();

        return view('leader.jobs.edit', compact('job', 'therapists', 'commissionRules'));
    }

    public function update(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_address' => 'required|string',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'service_type' => 'required|string|max:100',
            'job_date' => 'required|date',
            'job_end_date' => 'nullable|date|after_or_equal:job_date',
            'job_time' => 'required',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Allow self-assignment or team therapist
        $assignee = User::findOrFail($validated['assigned_to']);
        if ($assignee->id !== auth()->id() && $assignee->leader_id !== auth()->id()) {
            abort(403, 'This user is not in your team.');
        }

        $rule = CommissionRule::where('service_type', $validated['service_type'])->where('status', 'active')->first();
        $validated['service_category'] = $rule->service_category ?? 'wellness';
        $validated['work_days'] = $rule->work_days ?? null;

        if ($validated['service_category'] !== 'wellness' && $validated['work_days']) {
            $validated['job_end_date'] = Carbon::parse($validated['job_date'])->addDays($validated['work_days'] - 1)->format('Y-m-d');
        } else {
            $validated['job_end_date'] = null;
            $validated['work_days'] = null;
        }

        $oldDate = $job->job_date?->format('Y-m-d');
        $oldType = $job->service_type;
        $job->update($validated);

        if ($job->isMultiDay() && $job->work_days) {
            if ($oldDate !== $validated['job_date'] || $oldType !== $validated['service_type']) {
                $hasActivity = $job->dailyRecords()->where(function ($q) {
                    $q->whereNotNull('therapist_check_in_at')->orWhereNotNull('leader_check_in_at');
                })->exists();

                if (!$hasActivity) {
                    $job->dailyRecords()->delete();
                    $this->createDailyRecords($job);
                }
            }
        } else {
            $job->dailyRecords()->delete();
        }

        return redirect()->route('leader.jobs.index')->with('success', 'Job updated successfully.');
    }

    public function cancel(ServiceJob $job)
    {
        $this->authorizeJob($job);

        if (in_array($job->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This job cannot be cancelled.');
        }

        $job->update(['status' => 'cancelled']);

        return back()->with('success', 'Job has been cancelled.');
    }

    // ─── Job Execution (when leader is the assignee) ────────────────

    public function accept(ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->assigned_to !== auth()->id()) {
            return back()->with('error', 'You can only accept jobs assigned to you.');
        }

        if ($job->status !== 'pending') {
            return back()->with('error', 'Only pending jobs can be accepted.');
        }

        $job->update(['status' => 'accepted']);

        return back()->with('success', 'Job accepted successfully.');
    }

    // Wellness single-day check-in (leader doing the job)
    public function wellnessCheckIn(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->assigned_to !== auth()->id()) {
            return back()->with('error', 'You can only check in to jobs assigned to you.');
        }

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

    // Wellness single-day check-out (leader doing the job)
    public function wellnessCheckOut(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->assigned_to !== auth()->id()) {
            return back()->with('error', 'You can only check out from jobs assigned to you.');
        }

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

        return back()->with('success', 'Checked out successfully. Job completed! Commission and points awarded.');
    }

    // Multi-day daily check-in (leader doing the job as therapist)
    public function dailyCheckIn(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->assigned_to !== auth()->id()) {
            return back()->with('error', 'You can only check in to jobs assigned to you.');
        }

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

    // Multi-day daily check-out (leader doing the job as therapist)
    public function dailyCheckOut(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->assigned_to !== auth()->id()) {
            return back()->with('error', 'You can only check out from jobs assigned to you.');
        }

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

            return back()->with('success', 'Day ' . $record->day_number . ' completed. All days done — Job completed! Commission and points awarded.');
        }

        return back()->with('success', 'Checked out for Day ' . $record->day_number . '. ' . ($job->work_days - $record->day_number) . ' days remaining.');
    }

    // Leader supervision check-in for multi-day jobs (when NOT the assignee)
    public function checkIn(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if (!$job->isMultiDay()) {
            return back()->with('error', 'Leader check-in is only for Stay In / Daily Visit jobs.');
        }

        $request->validate([
            'day_id' => 'required|exists:job_daily_records,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $record = JobDailyRecord::where('id', $request->day_id)
            ->where('service_job_id', $job->id)
            ->firstOrFail();

        if ($record->leader_check_in_at) {
            return back()->with('error', 'Already checked in for this day.');
        }

        $record->update([
            'leader_check_in_at' => now(),
            'leader_check_in_lat' => $request->latitude,
            'leader_check_in_lng' => $request->longitude,
        ]);

        return back()->with('success', 'Leader checked in for Day ' . $record->day_number . '.');
    }

    // Leader supervision check-out for multi-day jobs (when NOT the assignee)
    public function checkOut(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if (!$job->isMultiDay()) {
            return back()->with('error', 'Leader check-out is only for Stay In / Daily Visit jobs.');
        }

        $request->validate([
            'day_id' => 'required|exists:job_daily_records,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $record = JobDailyRecord::where('id', $request->day_id)
            ->where('service_job_id', $job->id)
            ->firstOrFail();

        if (!$record->leader_check_in_at) {
            return back()->with('error', 'Must check in first.');
        }

        if ($record->leader_check_out_at) {
            return back()->with('error', 'Already checked out for this day.');
        }

        $record->update([
            'leader_check_out_at' => now(),
            'leader_check_out_lat' => $request->latitude,
            'leader_check_out_lng' => $request->longitude,
        ]);

        return back()->with('success', 'Leader checked out for Day ' . $record->day_number . '.');
    }

    // Post work update
    public function postUpdate(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        if ($job->assigned_to !== auth()->id()) {
            return back()->with('error', 'You can only post updates on jobs assigned to you.');
        }

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

    // Add/update notes
    public function addNotes(Request $request, ServiceJob $job)
    {
        $this->authorizeJob($job);

        $request->validate(['notes' => 'required|string|max:1000']);
        $job->update(['notes' => $request->notes]);

        return back()->with('success', 'Notes updated successfully.');
    }

    // ─── Private Helpers ────────────────────────────────────────────

    private function authorizeJob(ServiceJob $job)
    {
        if ($job->assigned_by !== auth()->id() && $job->assigned_to !== auth()->id()) {
            abort(403, 'Unauthorized access to this job.');
        }
    }

    private function createDailyRecords(ServiceJob $job): void
    {
        $startDate = Carbon::parse($job->job_date);
        for ($i = 1; $i <= $job->work_days; $i++) {
            JobDailyRecord::create([
                'service_job_id' => $job->id,
                'day_number' => $i,
                'date' => $startDate->copy()->addDays($i - 1)->format('Y-m-d'),
                'status' => 'pending',
            ]);
        }
    }

    private function generateJobCode($jobDate): string
    {
        $date = date('Ymd', strtotime($jobDate));
        $count = ServiceJob::whereDate('job_date', $jobDate)->count() + 1;
        return 'JOB-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
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

        // Direct commission (for whoever did the job)
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
            if ($affiliateAmt > 0 && $job->booking->referred_by_type === 'user') {
                Commission::create([
                    'user_id' => $job->booking->referred_by_id,
                    'service_job_id' => $job->id,
                    'type' => 'affiliate',
                    'amount' => $affiliateAmt,
                    'month' => $month,
                    'status' => 'pending',
                ]);
            }
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
