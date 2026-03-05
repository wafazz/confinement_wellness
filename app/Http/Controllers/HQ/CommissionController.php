<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Notifications\CommissionApproved;
use App\Notifications\CommissionPaid;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $commissions = Commission::with(['user', 'serviceJob']);

            if ($request->filled('filter_month')) {
                $commissions->where('month', $request->filter_month);
            }
            if ($request->filled('filter_status')) {
                $commissions->where('status', $request->filter_status);
            }
            if ($request->filled('filter_type')) {
                $commissions->where('type', $request->filter_type);
            }

            return DataTables::of($commissions)
                ->addColumn('user_name', fn($row) => $row->user->name ?? '-')
                ->addColumn('user_role', fn($row) => ucfirst($row->user->role ?? '-'))
                ->addColumn('job_code', fn($row) => $row->serviceJob->job_code ?? '-')
                ->addColumn('amount_fmt', fn($row) => 'RM ' . number_format($row->amount, 2))
                ->addColumn('type_badge', function ($row) {
                    $color = match ($row->type) {
                        'direct' => 'primary',
                        'override' => 'info',
                        'affiliate' => 'success',
                        default => 'secondary',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->type) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $color = match ($row->status) {
                        'pending' => 'warning',
                        'approved' => 'primary',
                        'paid' => 'success',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btns = '';
                    if ($row->status === 'pending') {
                        $approveUrl = route('hq.commissions.approve', $row->id);
                        $btns .= '<form action="' . $approveUrl . '" method="POST" class="d-inline">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="btn btn-sm btn-primary" title="Approve"><i class="fas fa-check"></i></button>'
                            . '</form> ';
                    }
                    if ($row->status === 'approved') {
                        $paidUrl = route('hq.commissions.mark-paid', $row->id);
                        $btns .= '<form action="' . $paidUrl . '" method="POST" class="d-inline">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="btn btn-sm btn-success" title="Mark Paid"><i class="fas fa-money-bill-wave"></i></button>'
                            . '</form> ';
                    }
                    return $btns ?: '<span class="text-muted small">-</span>';
                })
                ->rawColumns(['type_badge', 'status_badge', 'action'])
                ->make(true);
        }

        // Monthly summary stats
        $totalPending = Commission::where('status', 'pending')->sum('amount');
        $totalApproved = Commission::where('status', 'approved')->sum('amount');
        $totalPaid = Commission::where('status', 'paid')->sum('amount');
        $months = Commission::select('month')->distinct()->orderBy('month', 'desc')->pluck('month');

        return view('hq.commissions.index', compact('totalPending', 'totalApproved', 'totalPaid', 'months'));
    }

    public function approve(Commission $commission)
    {
        if ($commission->status !== 'pending') {
            return back()->with('error', 'Only pending commissions can be approved.');
        }

        $commission->update(['status' => 'approved']);
        $commission->user->notify(new CommissionApproved($commission));
        return back()->with('success', 'Commission approved.');
    }

    public function markPaid(Commission $commission)
    {
        if ($commission->status !== 'approved') {
            return back()->with('error', 'Only approved commissions can be marked as paid.');
        }

        $commission->update(['status' => 'paid', 'paid_at' => now()]);
        $commission->user->notify(new CommissionPaid($commission));
        return back()->with('success', 'Commission marked as paid.');
    }

    public function bulkApprove(Request $request)
    {
        $month = $request->input('month');
        if (!$month) {
            return back()->with('error', 'Please select a month.');
        }

        $count = Commission::where('status', 'pending')->where('month', $month)->update(['status' => 'approved']);
        return back()->with('success', $count . ' commissions approved for ' . $month . '.');
    }

    public function bulkPaid(Request $request)
    {
        $month = $request->input('month');
        if (!$month) {
            return back()->with('error', 'Please select a month.');
        }

        $count = Commission::where('status', 'approved')->where('month', $month)->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', $count . ' commissions marked as paid for ' . $month . '.');
    }

    public function downloadPdf(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $commissions = Commission::with(['user', 'serviceJob'])
            ->where('month', $month)
            ->orderBy('type')
            ->orderBy('user_id')
            ->get();

        $totalDirect = $commissions->where('type', 'direct')->sum('amount');
        $totalOverride = $commissions->where('type', 'override')->sum('amount');
        $totalAffiliate = $commissions->where('type', 'affiliate')->sum('amount');
        $grandTotal = $commissions->sum('amount');

        $pdf = Pdf::loadView('pdf.hq-report', compact(
            'commissions', 'totalDirect', 'totalOverride', 'totalAffiliate', 'grandTotal', 'month'
        ));
        return $pdf->download('hq-commission-report-' . $month . '.pdf');
    }
}
