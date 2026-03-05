<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\ServiceJob;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getReportData($request);
        return view('hq.reports.index', $data);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getReportData($request);
        $pdf = Pdf::loadView('pdf.hq-summary-report', $data);
        $filename = 'cw-report-' . $data['period'] . '-' . $data['dateInput'] . '.pdf';
        return $pdf->download($filename);
    }

    public function downloadCsv(Request $request)
    {
        $data = $this->getReportData($request);
        $filename = 'cw-report-' . $data['period'] . '-' . $data['dateInput'] . '.csv';

        return new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['C&W Summary Report']);
            fputcsv($handle, ['Period', ucfirst($data['period'])]);
            fputcsv($handle, ['Date Range', $data['startDate']->format('d M Y') . ' - ' . $data['endDate']->format('d M Y')]);
            fputcsv($handle, []);

            // Job Stats
            fputcsv($handle, ['--- JOB STATISTICS ---']);
            fputcsv($handle, ['Total Jobs', $data['totalJobs']]);
            fputcsv($handle, ['Completed', $data['completedJobs']]);
            fputcsv($handle, ['Pending', $data['pendingJobs']]);
            fputcsv($handle, ['Cancelled', $data['cancelledJobs']]);
            fputcsv($handle, []);

            // Commission
            fputcsv($handle, ['--- COMMISSION BREAKDOWN ---']);
            fputcsv($handle, ['Total Commission (RM)', number_format($data['totalCommission'], 2)]);
            fputcsv($handle, ['Direct (RM)', number_format($data['directCommission'], 2)]);
            fputcsv($handle, ['Override (RM)', number_format($data['overrideCommission'], 2)]);
            fputcsv($handle, ['Affiliate (RM)', number_format($data['affiliateCommission'], 2)]);
            fputcsv($handle, []);

            // Bookings
            fputcsv($handle, ['--- BOOKING STATISTICS ---']);
            fputcsv($handle, ['Total Bookings', $data['totalBookings']]);
            fputcsv($handle, ['Converted', $data['convertedBookings']]);
            fputcsv($handle, ['Pending Review', $data['pendingBookings']]);
            fputcsv($handle, []);

            // Active Agents
            fputcsv($handle, ['--- ACTIVE AGENTS ---']);
            fputcsv($handle, ['Leaders', $data['activeLeaders']]);
            fputcsv($handle, ['Therapists', $data['activeTherapists']]);
            fputcsv($handle, []);

            // Jobs by Service Type
            fputcsv($handle, ['--- JOBS BY SERVICE TYPE ---']);
            fputcsv($handle, ['Service Type', 'Count']);
            foreach ($data['jobsByServiceType'] as $row) {
                fputcsv($handle, [$row->service_type, $row->total]);
            }
            fputcsv($handle, []);

            // Jobs by State
            fputcsv($handle, ['--- JOBS BY STATE ---']);
            fputcsv($handle, ['State', 'Count']);
            foreach ($data['jobsByState'] as $row) {
                fputcsv($handle, [$row->state, $row->total]);
            }
            fputcsv($handle, []);

            // Top 5 Therapists
            fputcsv($handle, ['--- TOP 5 THERAPISTS ---']);
            fputcsv($handle, ['Name', 'Completed Jobs', 'Commission (RM)']);
            foreach ($data['topTherapists'] as $t) {
                fputcsv($handle, [$t->name, $t->jobs_count, number_format($t->commission_total, 2)]);
            }
            fputcsv($handle, []);

            // Top 5 Leaders
            fputcsv($handle, ['--- TOP 5 LEADERS ---']);
            fputcsv($handle, ['Name', 'Team Jobs', 'Override (RM)']);
            foreach ($data['topLeaders'] as $l) {
                fputcsv($handle, [$l->name, $l->jobs_count, number_format($l->commission_total, 2)]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function getReportData(Request $request): array
    {
        $period = $request->input('period', 'monthly');
        $dateInput = $request->input('date', '');

        $now = Carbon::now();

        switch ($period) {
            case 'daily':
                $date = $dateInput ? Carbon::parse($dateInput) : $now;
                $startDate = $date->copy()->startOfDay();
                $endDate = $date->copy()->endOfDay();
                $dateInput = $dateInput ?: $now->format('Y-m-d');
                break;

            case 'weekly':
                if ($dateInput && preg_match('/^\d{4}-W\d{2}$/', $dateInput)) {
                    $parts = explode('-W', $dateInput);
                    $startDate = Carbon::now()->setISODate((int) $parts[0], (int) $parts[1])->startOfWeek();
                } else {
                    $startDate = $now->copy()->startOfWeek();
                    $dateInput = $now->format('Y-\\WW');
                }
                $endDate = $startDate->copy()->endOfWeek();
                break;

            case 'yearly':
                $year = $dateInput ? (int) $dateInput : $now->year;
                $startDate = Carbon::create($year, 1, 1)->startOfDay();
                $endDate = Carbon::create($year, 12, 31)->endOfDay();
                $dateInput = $dateInput ?: (string) $now->year;
                break;

            default: // monthly
                $period = 'monthly';
                if ($dateInput && preg_match('/^\d{4}-\d{2}$/', $dateInput)) {
                    $startDate = Carbon::parse($dateInput . '-01')->startOfMonth();
                } else {
                    $startDate = $now->copy()->startOfMonth();
                    $dateInput = $now->format('Y-m');
                }
                $endDate = $startDate->copy()->endOfMonth();
                break;
        }

        // Job stats
        $jobsQuery = ServiceJob::whereBetween('created_at', [$startDate, $endDate]);
        $totalJobs = (clone $jobsQuery)->count();
        $completedJobs = (clone $jobsQuery)->where('status', 'completed')->count();
        $pendingJobs = (clone $jobsQuery)->where('status', 'pending')->count();
        $cancelledJobs = (clone $jobsQuery)->where('status', 'cancelled')->count();

        // Commission breakdown
        $commQuery = Commission::whereBetween('created_at', [$startDate, $endDate]);
        $totalCommission = (clone $commQuery)->sum('amount');
        $directCommission = (clone $commQuery)->where('type', 'direct')->sum('amount');
        $overrideCommission = (clone $commQuery)->where('type', 'override')->sum('amount');
        $affiliateCommission = (clone $commQuery)->where('type', 'affiliate')->sum('amount');

        // Booking stats
        $bookingQuery = Booking::whereBetween('created_at', [$startDate, $endDate]);
        $totalBookings = (clone $bookingQuery)->count();
        $convertedBookings = (clone $bookingQuery)->where('status', 'converted')->count();
        $pendingBookings = (clone $bookingQuery)->where('status', 'pending_review')->count();

        // Active agents
        $activeLeaders = User::where('role', 'leader')->where('status', 'active')->count();
        $activeTherapists = User::where('role', 'therapist')->where('status', 'active')->count();

        // Jobs by service type
        $jobsByServiceType = ServiceJob::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('service_type, count(*) as total')
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get();

        // Jobs by state
        $jobsByState = ServiceJob::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('state, count(*) as total')
            ->groupBy('state')
            ->orderByDesc('total')
            ->get();

        // Top 5 therapists (by completed jobs in period)
        $topTherapists = User::where('role', 'therapist')
            ->withCount(['assignedJobs as jobs_count' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['commissions as commission_total' => function ($q) use ($startDate, $endDate) {
                $q->where('type', 'direct')->whereBetween('created_at', [$startDate, $endDate]);
            }], 'amount')
            ->orderByDesc('jobs_count')
            ->limit(5)
            ->get();

        // Top 5 leaders (by team jobs)
        $topLeaders = User::where('role', 'leader')
            ->withCount(['createdJobs as jobs_count' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['commissions as commission_total' => function ($q) use ($startDate, $endDate) {
                $q->where('type', 'override')->whereBetween('created_at', [$startDate, $endDate]);
            }], 'amount')
            ->orderByDesc('jobs_count')
            ->limit(5)
            ->get();

        return compact(
            'period', 'dateInput', 'startDate', 'endDate',
            'totalJobs', 'completedJobs', 'pendingJobs', 'cancelledJobs',
            'totalCommission', 'directCommission', 'overrideCommission', 'affiliateCommission',
            'totalBookings', 'convertedBookings', 'pendingBookings',
            'activeLeaders', 'activeTherapists',
            'jobsByServiceType', 'jobsByState',
            'topTherapists', 'topLeaders'
        );
    }
}
