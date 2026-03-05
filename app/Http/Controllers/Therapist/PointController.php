<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\RewardTier;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentMonth = now()->format('Y-m');

        $totalPoints = Point::where('user_id', $user->id)->sum('points');
        $pointsThisMonth = Point::where('user_id', $user->id)
            ->where('month', $currentMonth)->sum('points');

        // Monthly breakdown (last 6 months)
        $monthlyBreakdown = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $monthlyBreakdown[$m] = Point::where('user_id', $user->id)
                ->where('month', $m)->sum('points');
        }

        // Per-job breakdown
        $pointRecords = Point::with('serviceJob')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        // Tier info
        $currentTier = RewardTier::where('min_points', '<=', $totalPoints)
            ->where('status', 'active')
            ->orderByDesc('min_points')
            ->first();
        $nextTier = RewardTier::where('min_points', '>', $totalPoints)
            ->where('status', 'active')
            ->orderBy('min_points')
            ->first();
        $allTiers = RewardTier::where('status', 'active')
            ->orderBy('min_points')
            ->get();

        return view('therapist.points.index', compact(
            'user', 'totalPoints', 'pointsThisMonth',
            'monthlyBreakdown', 'pointRecords',
            'currentTier', 'nextTier', 'allTiers'
        ));
    }
}
