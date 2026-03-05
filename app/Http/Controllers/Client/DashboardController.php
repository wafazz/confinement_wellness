<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();

        $totalBookings = $client->bookings()->count();
        $pendingBookings = $client->bookings()->where('status', 'pending_review')->count();
        $activeJobs = $client->serviceJobs()->whereIn('status', ['pending', 'accepted', 'checked_in'])->count();
        $completedJobs = $client->serviceJobs()->where('status', 'completed')->count();
        $rewardPoints = $client->reward_points ?? 0;

        $recentBookings = $client->bookings()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $activeServiceJobs = $client->serviceJobs()
            ->with('assignee')
            ->whereIn('status', ['pending', 'accepted', 'checked_in'])
            ->orderBy('job_date')
            ->get();

        return view('client.dashboard', compact(
            'client', 'totalBookings', 'pendingBookings',
            'activeJobs', 'completedJobs', 'rewardPoints',
            'recentBookings', 'activeServiceJobs'
        ));
    }
}
