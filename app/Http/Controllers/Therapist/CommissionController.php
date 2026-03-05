<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        // Own direct commissions
        $commissions = Commission::with('serviceJob')
            ->where('user_id', auth()->id())
            ->whereIn('type', ['direct', 'affiliate'])
            ->where('month', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        // Summary (direct only for backward compat)
        $directCommissions = $commissions->where('type', 'direct');
        $affiliateCommissions = $commissions->where('type', 'affiliate');
        $totalEarned = $commissions->sum('amount');
        $totalPending = $commissions->where('status', 'pending')->sum('amount');
        $totalApproved = $commissions->where('status', 'approved')->sum('amount');
        $totalPaid = $commissions->where('status', 'paid')->sum('amount');
        $affiliateTotal = $affiliateCommissions->sum('amount');

        // All-time totals
        $allTimeTotal = Commission::where('user_id', auth()->id())->whereIn('type', ['direct', 'affiliate'])->sum('amount');
        $allTimePaid = Commission::where('user_id', auth()->id())->whereIn('type', ['direct', 'affiliate'])->where('status', 'paid')->sum('amount');

        // Available months
        $months = Commission::where('user_id', auth()->id())
            ->select('month')->distinct()->orderBy('month', 'desc')->pluck('month');

        return view('therapist.commissions.index', compact(
            'commissions', 'totalEarned', 'totalPending', 'totalApproved', 'totalPaid',
            'allTimeTotal', 'allTimePaid', 'affiliateTotal', 'month', 'months'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $user = auth()->user();

        $commissions = Commission::with('serviceJob')
            ->where('user_id', $user->id)
            ->whereIn('type', ['direct', 'affiliate'])
            ->where('month', $month)
            ->orderBy('created_at')
            ->get();

        $total = $commissions->sum('amount');

        $pdf = Pdf::loadView('pdf.therapist-statement', compact('user', 'commissions', 'total', 'month'));
        return $pdf->download('commission-statement-' . $month . '.pdf');
    }
}
