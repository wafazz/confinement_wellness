<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Point;
use App\Models\RewardTier;
use App\Models\ServiceJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teamIds = $user->therapists()->pluck('id')->toArray();
        $currentMonth = now()->format('Y-m');

        // Team stats
        $teamSize = count($teamIds);
        $pendingJobsCount = ServiceJob::where('assigned_by', $user->id)
            ->where('status', 'pending')->count();

        // Stat cards
        $totalJobsCompleted = ServiceJob::whereIn('assigned_to', $teamIds)
            ->where('status', 'completed')->count();
        $totalJobsThisMonth = ServiceJob::whereIn('assigned_to', $teamIds)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)->count();

        $directCommission = Commission::where('user_id', $user->id)
            ->where('type', 'override')->sum('amount');
        $directCommissionThisMonth = Commission::where('user_id', $user->id)
            ->where('type', 'override')
            ->where('month', $currentMonth)->sum('amount');

        // Active jobs (not completed/cancelled)
        $activeJobs = ServiceJob::whereIn('assigned_to', $teamIds)
            ->whereIn('status', ['pending', 'accepted', 'checked_in'])
            ->with('assignee')
            ->orderBy('job_date', 'desc')
            ->limit(5)
            ->get();

        // Trainer leaderboard — team therapists ranked by commission
        $leaderboard = DB::table('users')
            ->leftJoin('commissions', function ($join) {
                $join->on('users.id', '=', 'commissions.user_id')
                    ->where('commissions.type', '=', 'direct');
            })
            ->leftJoin('points', 'users.id', '=', 'points.user_id')
            ->where('users.leader_id', $user->id)
            ->where('users.role', 'therapist')
            ->groupBy('users.id', 'users.name')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COALESCE(SUM(DISTINCT commissions.amount), 0) as total_commission'),
                DB::raw('COALESCE(SUM(DISTINCT points.points), 0) as total_points')
            )
            ->orderByDesc('total_commission')
            ->limit(5)
            ->get();

        // Earnings last 6 months (override commission)
        $earningsChart = Commission::where('user_id', $user->id)
            ->where('type', 'override')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->select('month', DB::raw('SUM(amount) as total'))
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $chartLabels[] = now()->subMonths($i)->format('M');
            $chartData[] = (float) ($earningsChart[$m] ?? 0);
        }

        // Earnings breakdown
        $downlineCommission = Commission::where('user_id', $user->id)
            ->where('type', 'override')->sum('amount');
        $teamDirectCommission = Commission::whereIn('user_id', $teamIds)
            ->where('type', 'direct')->sum('amount');

        // Points + reward progress
        $totalPoints = Point::whereIn('user_id', $teamIds)->sum('points')
            + Point::where('user_id', $user->id)->sum('points');
        $nextTier = RewardTier::where('min_points', '>', $totalPoints)
            ->where('status', 'active')
            ->orderBy('min_points')
            ->first();
        $currentTier = RewardTier::where('min_points', '<=', $totalPoints)
            ->where('status', 'active')
            ->orderByDesc('min_points')
            ->first();

        // Booking stats for leader's state
        $pendingBookings = Booking::where('state', $user->state)
            ->where('status', 'pending_review')->count();

        return view('leader.dashboard', compact(
            'user', 'teamSize', 'pendingJobsCount', 'pendingBookings',
            'totalJobsCompleted', 'totalJobsThisMonth', 'directCommission', 'directCommissionThisMonth',
            'activeJobs', 'leaderboard',
            'chartLabels', 'chartData',
            'downlineCommission', 'teamDirectCommission',
            'totalPoints', 'nextTier', 'currentTier'
        ));
    }
}
