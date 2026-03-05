<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Point;
use App\Models\RewardTier;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentMonth = now()->format('Y-m');

        // Stat cards
        $totalJobsCompleted = ServiceJob::where('assigned_to', $user->id)
            ->where('status', 'completed')->count();
        $jobsThisMonth = ServiceJob::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)->count();

        $totalCommission = Commission::where('user_id', $user->id)
            ->where('type', 'direct')->sum('amount');
        $commissionThisMonth = Commission::where('user_id', $user->id)
            ->where('type', 'direct')
            ->where('month', $currentMonth)->sum('amount');

        $totalPoints = Point::where('user_id', $user->id)->sum('points');
        $pointsThisMonth = Point::where('user_id', $user->id)
            ->where('month', $currentMonth)->sum('points');

        // Upcoming / active jobs
        $activeJobs = ServiceJob::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'accepted', 'checked_in'])
            ->orderBy('job_date')
            ->orderBy('job_time')
            ->limit(5)
            ->get();

        // Ranking — nationwide among therapists
        $rankings = DB::table('users')
            ->leftJoin('points', 'users.id', '=', 'points.user_id')
            ->where('users.role', 'therapist')
            ->where('users.status', 'active')
            ->groupBy('users.id', 'users.name')
            ->select('users.id', 'users.name', DB::raw('COALESCE(SUM(points.points), 0) as total_points'))
            ->orderByDesc('total_points')
            ->get();

        $myRank = $rankings->search(fn($r) => $r->id === $user->id);
        $myRank = $myRank !== false ? $myRank + 1 : null;
        $topTherapists = $rankings->take(5);

        // Earnings chart last 6 months
        $earningsChart = Commission::where('user_id', $user->id)
            ->where('type', 'direct')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->select('month', DB::raw('SUM(amount) as total'))
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $chartLabels[] = now()->subMonths($i)->format('M');
            $chartData[] = (float) ($earningsChart[$m] ?? 0);
        }

        // Points + reward tiers
        $nextTier = RewardTier::where('min_points', '>', $totalPoints)
            ->where('status', 'active')
            ->orderBy('min_points')
            ->first();
        $currentTier = RewardTier::where('min_points', '<=', $totalPoints)
            ->where('status', 'active')
            ->orderByDesc('min_points')
            ->first();

        return view('therapist.dashboard', compact(
            'user', 'totalJobsCompleted', 'jobsThisMonth',
            'totalCommission', 'commissionThisMonth',
            'totalPoints', 'pointsThisMonth',
            'activeJobs', 'myRank', 'topTherapists',
            'chartLabels', 'chartData',
            'nextTier', 'currentTier'
        ));
    }
}
