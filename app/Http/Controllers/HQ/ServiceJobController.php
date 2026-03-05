<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Models\JobDailyRecord;
use App\Models\User;
use App\Models\CommissionRule;
use App\Notifications\JobAssigned;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ServiceJobController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jobs = ServiceJob::with(['assigner', 'assignee']);

            if ($request->filled('filter_status')) {
                $jobs->where('status', $request->filter_status);
            }
            if ($request->filled('filter_state')) {
                $jobs->where('state', $request->filter_state);
            }
            if ($request->filled('filter_date_from')) {
                $jobs->where('job_date', '>=', $request->filter_date_from);
            }
            if ($request->filled('filter_date_to')) {
                $jobs->where('job_date', '<=', $request->filter_date_to);
            }

            return DataTables::of($jobs)
                ->addColumn('assigned_by_name', fn($row) => $row->assigner->name ?? '-')
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
                    $viewUrl = route('hq.jobs.show', $row->id);
                    $editUrl = route('hq.jobs.edit', $row->id);
                    $btns = '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a> ';
                    $btns .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a> ';
                    if ($row->status !== 'completed' && $row->status !== 'cancelled') {
                        $cancelUrl = route('hq.jobs.cancel', $row->id);
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

        return view('hq.jobs.index');
    }

    public function create()
    {
        $leaders = User::where('role', 'leader')->where('status', 'active')->get();
        $therapists = User::where('role', 'therapist')->where('status', 'active')->get();
        $commissionRules = CommissionRule::where('status', 'active')->get();

        return view('hq.jobs.create', compact('leaders', 'therapists', 'commissionRules'));
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
            'assigned_by' => 'required|exists:users,id',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $rule = CommissionRule::where('service_type', $validated['service_type'])->where('status', 'active')->first();

        $validated['service_category'] = $rule->service_category ?? 'wellness';
        $validated['work_days'] = $rule->work_days ?? null;
        $validated['job_code'] = $this->generateJobCode($validated['job_date']);
        $validated['status'] = 'pending';

        if ($validated['service_category'] !== 'wellness' && $validated['work_days']) {
            $validated['job_end_date'] = Carbon::parse($validated['job_date'])->addDays($validated['work_days'] - 1)->format('Y-m-d');
        } else {
            $validated['job_end_date'] = null;
            $validated['work_days'] = null;
        }

        $job = ServiceJob::create($validated);

        // Create daily records for multi-day jobs
        if ($job->isMultiDay() && $job->work_days) {
            $this->createDailyRecords($job);
        }

        $assignee = User::find($validated['assigned_to']);
        if ($assignee) {
            $assignee->notify(new JobAssigned($job));
        }

        return redirect()->route('hq.jobs.index')->with('success', 'Job created successfully.');
    }

    public function show(ServiceJob $job)
    {
        $job->load(['assigner', 'assignee', 'commissions.user', 'points.user', 'dailyRecords', 'updates.user', 'review.client']);
        return view('hq.jobs.show', compact('job'));
    }

    public function edit(ServiceJob $job)
    {
        $leaders = User::where('role', 'leader')->where('status', 'active')->get();
        $therapists = User::where('role', 'therapist')->where('status', 'active')->get();
        $commissionRules = CommissionRule::where('status', 'active')->get();

        return view('hq.jobs.edit', compact('job', 'leaders', 'therapists', 'commissionRules'));
    }

    public function update(Request $request, ServiceJob $job)
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
            'assigned_by' => 'required|exists:users,id',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

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

        // Rebuild daily records if date or type changed and job hasn't started
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

        return redirect()->route('hq.jobs.index')->with('success', 'Job updated successfully.');
    }

    public function cancel(ServiceJob $job)
    {
        if (in_array($job->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This job cannot be cancelled.');
        }

        $job->update(['status' => 'cancelled']);

        return back()->with('success', 'Job has been cancelled.');
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
}
