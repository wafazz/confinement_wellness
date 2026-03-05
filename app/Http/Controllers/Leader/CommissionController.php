<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        // Own override commissions
        $ownCommissions = Commission::with('serviceJob.assignee')
            ->where('user_id', auth()->id())
            ->where('type', 'override')
            ->where('month', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        // Own affiliate commissions
        $affiliateCommissions = Commission::with('serviceJob.assignee')
            ->where('user_id', auth()->id())
            ->where('type', 'affiliate')
            ->where('month', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        // Team direct commissions (therapists under this leader)
        $teamIds = auth()->user()->therapists()->pluck('id');
        $teamCommissions = Commission::with(['user', 'serviceJob'])
            ->whereIn('user_id', $teamIds)
            ->where('type', 'direct')
            ->where('month', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        // Monthly summary
        $ownTotal = $ownCommissions->sum('amount');
        $ownPending = $ownCommissions->where('status', 'pending')->sum('amount');
        $ownPaid = $ownCommissions->where('status', 'paid')->sum('amount');
        $affiliateTotal = $affiliateCommissions->sum('amount');

        $teamTotal = $teamCommissions->sum('amount');

        // Available months
        $months = Commission::where(function ($q) use ($teamIds) {
            $q->where('user_id', auth()->id())->orWhereIn('user_id', $teamIds);
        })->select('month')->distinct()->orderBy('month', 'desc')->pluck('month');

        return view('leader.commissions.index', compact(
            'ownCommissions', 'affiliateCommissions', 'teamCommissions',
            'ownTotal', 'ownPending', 'ownPaid', 'affiliateTotal', 'teamTotal', 'month', 'months'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $user = auth()->user();

        $teamIds = $user->therapists()->pluck('id');

        $ownCommissions = Commission::with('serviceJob.assignee')
            ->where('user_id', $user->id)
            ->where('type', 'override')
            ->where('month', $month)
            ->orderBy('created_at')
            ->get();

        $teamCommissions = Commission::with(['user', 'serviceJob'])
            ->whereIn('user_id', $teamIds)
            ->where('type', 'direct')
            ->where('month', $month)
            ->orderBy('created_at')
            ->get();

        $ownTotal = $ownCommissions->sum('amount');
        $teamTotal = $teamCommissions->sum('amount');

        $pdf = Pdf::loadView('pdf.leader-summary', compact(
            'user', 'ownCommissions', 'teamCommissions', 'ownTotal', 'teamTotal', 'month'
        ));
        return $pdf->download('team-commission-summary-' . $month . '.pdf');
    }
}
