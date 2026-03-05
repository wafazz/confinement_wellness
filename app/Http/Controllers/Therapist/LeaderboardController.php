<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'nationwide');
        $month = $request->get('month', now()->format('Y-m'));

        $query = DB::table('users')
            ->leftJoin('points', function ($join) use ($month) {
                $join->on('users.id', '=', 'points.user_id')
                    ->where('points.month', '=', $month);
            })
            ->where('users.role', 'therapist')
            ->where('users.status', 'active');

        if ($filter === 'state' && $user->state) {
            $query->where('users.state', $user->state);
        } elseif ($filter === 'team' && $user->leader_id) {
            $query->where('users.leader_id', $user->leader_id);
        }

        $rankings = $query
            ->groupBy('users.id', 'users.name', 'users.state')
            ->select(
                'users.id',
                'users.name',
                'users.state',
                DB::raw('COALESCE(SUM(points.points), 0) as total_points')
            )
            ->orderByDesc('total_points')
            ->get();

        $myRank = $rankings->search(fn($r) => $r->id === $user->id);
        $myRank = $myRank !== false ? $myRank + 1 : null;
        $myPoints = $rankings->firstWhere('id', $user->id)?->total_points ?? 0;

        // Get all months that have points data for the selector
        $availableMonths = DB::table('points')
            ->select('month')
            ->distinct()
            ->orderByDesc('month')
            ->pluck('month');

        return view('therapist.leaderboard.index', compact(
            'user', 'rankings', 'myRank', 'myPoints',
            'filter', 'month', 'availableMonths'
        ));
    }
}
