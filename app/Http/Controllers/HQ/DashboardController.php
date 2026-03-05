<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = now()->format('Y-m');

        // Stat cards
        $totalLeaders = User::where('role', 'leader')->count();
        $totalTherapists = User::where('role', 'therapist')->count();
        $activeAgents = User::whereIn('role', ['leader', 'therapist'])
            ->where('status', 'active')->count();

        $totalJobsAllTime = ServiceJob::count();
        $totalJobsThisMonth = ServiceJob::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $completedJobsThisMonth = ServiceJob::where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)->count();

        $totalCommissionThisMonth = Commission::where('month', $currentMonth)->sum('amount');
        $totalCommissionPending = Commission::where('status', 'pending')->sum('amount');
        $totalCommissionPaid = Commission::where('status', 'paid')->sum('amount');

        // Jobs by status (pie chart)
        $jobsByStatus = ServiceJob::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusLabels = ['pending', 'accepted', 'checked_in', 'completed', 'cancelled'];
        $statusColors = ['#6c757d', '#0d6efd', '#fd7e14', '#198754', '#dc3545'];
        $statusData = [];
        foreach ($statusLabels as $s) {
            $statusData[] = $jobsByStatus[$s] ?? 0;
        }

        // Jobs by state (bar chart)
        $jobsByState = ServiceJob::select('state', DB::raw('COUNT(*) as total'))
            ->groupBy('state')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'state')
            ->toArray();

        $stateLabels = array_keys($jobsByState);
        $stateData = array_values($jobsByState);

        // Monthly jobs trend (last 6 months)
        $monthlyJobs = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $monthlyLabels[] = $m->format('M');
            $monthlyJobs[] = ServiceJob::where('status', 'completed')
                ->whereMonth('completed_at', $m->month)
                ->whereYear('completed_at', $m->year)->count();
        }

        // Top 10 therapists by jobs completed
        $topTherapists = User::where('role', 'therapist')
            ->withCount(['assignedJobs as completed_jobs' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['commissions as total_commission' => function ($q) {
                $q->where('type', 'direct');
            }], 'amount')
            ->withSum(['points as total_points'], 'points')
            ->orderByDesc('completed_jobs')
            ->limit(10)
            ->get();

        // Recent activity (latest 10 completed jobs)
        $recentActivity = ServiceJob::with(['assignee', 'assigner'])
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->limit(10)
            ->get();

        // Pending actions
        $pendingJobs = ServiceJob::where('status', 'pending')->count();
        $pendingCommissions = Commission::where('status', 'pending')->count();
        $pendingUsers = User::where('status', 'pending')->count();

        // Booking stats
        $pendingBookings = Booking::where('status', 'pending_review')->count();
        $todayBookings = Booking::whereDate('created_at', today())->count();

        return view('hq.dashboard', compact(
            'totalLeaders', 'totalTherapists', 'activeAgents',
            'totalJobsAllTime', 'totalJobsThisMonth', 'completedJobsThisMonth',
            'totalCommissionThisMonth', 'totalCommissionPending', 'totalCommissionPaid',
            'statusLabels', 'statusColors', 'statusData',
            'stateLabels', 'stateData',
            'monthlyLabels', 'monthlyJobs',
            'topTherapists', 'recentActivity',
            'pendingJobs', 'pendingCommissions', 'pendingUsers',
            'pendingBookings', 'todayBookings'
        ));
    }
}
