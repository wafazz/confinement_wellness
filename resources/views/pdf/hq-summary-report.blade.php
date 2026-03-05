<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1e293b; padding-bottom: 15px; }
        .header h1 { color: #1e293b; font-size: 20px; margin: 0; }
        .header p { color: #64748b; margin: 5px 0 0; font-size: 11px; }
        .summary-box { background: #f1f5f9; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; }
        .summary-box strong { color: #1e293b; }
        .section-title { color: #1e293b; font-size: 14px; font-weight: bold; margin: 20px 0 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.data th { background: #1e293b; color: #fff; padding: 7px 10px; text-align: left; font-size: 11px; }
        table.data td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        table.data tr:nth-child(even) { background: #f8fafc; }
        .two-col { width: 100%; }
        .two-col td { width: 50%; vertical-align: top; padding: 0 8px 0 0; }
        .two-col td:last-child { padding: 0 0 0 8px; }
        .kv-table { width: 100%; margin-bottom: 10px; }
        .kv-table td { padding: 4px 8px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
        .kv-table td:first-child { color: #64748b; width: 55%; }
        .kv-table td:last-child { font-weight: bold; text-align: right; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>C&W System &mdash; Summary Report</h1>
        <p>{{ ucfirst($period) }} Report: {{ $startDate->format('d M Y') }} &mdash; {{ $endDate->format('d M Y') }}</p>
        <p>Generated {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="summary-box">
        <strong>Overview:</strong>
        Jobs: <strong>{{ $totalJobs }}</strong> ({{ $completedJobs }} completed) |
        Commission: <strong>RM {{ number_format($totalCommission, 2) }}</strong> |
        Bookings: <strong>{{ $totalBookings }}</strong> |
        Active Agents: <strong>{{ $activeLeaders + $activeTherapists }}</strong>
    </div>

    {{-- Two-column layout: Jobs + Commission --}}
    <table class="two-col"><tr>
        <td>
            <div class="section-title">Job Statistics</div>
            <table class="kv-table">
                <tr><td>Total Jobs</td><td>{{ $totalJobs }}</td></tr>
                <tr><td>Completed</td><td>{{ $completedJobs }}</td></tr>
                <tr><td>Pending</td><td>{{ $pendingJobs }}</td></tr>
                <tr><td>Cancelled</td><td>{{ $cancelledJobs }}</td></tr>
            </table>
        </td>
        <td>
            <div class="section-title">Commission Breakdown</div>
            <table class="kv-table">
                <tr><td>Direct</td><td>RM {{ number_format($directCommission, 2) }}</td></tr>
                <tr><td>Override</td><td>RM {{ number_format($overrideCommission, 2) }}</td></tr>
                <tr><td>Affiliate</td><td>RM {{ number_format($affiliateCommission, 2) }}</td></tr>
                <tr><td>Total</td><td>RM {{ number_format($totalCommission, 2) }}</td></tr>
            </table>
        </td>
    </tr></table>

    {{-- Two-column layout: Bookings + Agents --}}
    <table class="two-col"><tr>
        <td>
            <div class="section-title">Booking Statistics</div>
            <table class="kv-table">
                <tr><td>Total Bookings</td><td>{{ $totalBookings }}</td></tr>
                <tr><td>Converted</td><td>{{ $convertedBookings }}</td></tr>
                <tr><td>Pending Review</td><td>{{ $pendingBookings }}</td></tr>
            </table>
        </td>
        <td>
            <div class="section-title">Active Agents</div>
            <table class="kv-table">
                <tr><td>Leaders</td><td>{{ $activeLeaders }}</td></tr>
                <tr><td>Therapists</td><td>{{ $activeTherapists }}</td></tr>
                <tr><td>Total</td><td>{{ $activeLeaders + $activeTherapists }}</td></tr>
            </table>
        </td>
    </tr></table>

    {{-- Two-column: Service Type + State --}}
    <table class="two-col"><tr>
        <td>
            <div class="section-title">Jobs by Service Type</div>
            @if($jobsByServiceType->isNotEmpty())
            <table class="data">
                <thead><tr><th>Service Type</th><th style="text-align:right;">Count</th></tr></thead>
                <tbody>
                    @foreach($jobsByServiceType as $row)
                    <tr><td>{{ $row->service_type }}</td><td style="text-align:right;">{{ $row->total }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color:#64748b;font-size:11px;">No data for this period.</p>
            @endif
        </td>
        <td>
            <div class="section-title">Jobs by State</div>
            @if($jobsByState->isNotEmpty())
            <table class="data">
                <thead><tr><th>State</th><th style="text-align:right;">Count</th></tr></thead>
                <tbody>
                    @foreach($jobsByState as $row)
                    <tr><td>{{ $row->state }}</td><td style="text-align:right;">{{ $row->total }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color:#64748b;font-size:11px;">No data for this period.</p>
            @endif
        </td>
    </tr></table>

    {{-- Top Performers --}}
    <table class="two-col"><tr>
        <td>
            <div class="section-title">Top 5 Therapists</div>
            @if($topTherapists->where('jobs_count', '>', 0)->isNotEmpty())
            <table class="data">
                <thead><tr><th>#</th><th>Name</th><th style="text-align:right;">Jobs</th><th style="text-align:right;">Commission</th></tr></thead>
                <tbody>
                    @foreach($topTherapists as $i => $t)
                    @if($t->jobs_count > 0)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $t->name }}</td>
                        <td style="text-align:right;">{{ $t->jobs_count }}</td>
                        <td style="text-align:right;">RM {{ number_format($t->commission_total ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color:#64748b;font-size:11px;">No data for this period.</p>
            @endif
        </td>
        <td>
            <div class="section-title">Top 5 Leaders</div>
            @if($topLeaders->where('jobs_count', '>', 0)->isNotEmpty())
            <table class="data">
                <thead><tr><th>#</th><th>Name</th><th style="text-align:right;">Jobs</th><th style="text-align:right;">Override</th></tr></thead>
                <tbody>
                    @foreach($topLeaders as $i => $l)
                    @if($l->jobs_count > 0)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $l->name }}</td>
                        <td style="text-align:right;">{{ $l->jobs_count }}</td>
                        <td style="text-align:right;">RM {{ number_format($l->commission_total ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color:#64748b;font-size:11px;">No data for this period.</p>
            @endif
        </td>
    </tr></table>

    <div class="footer">
        <p>This is a system-generated document. No signature required.</p>
        <p>Confinement & Women Wellness Agent Management System</p>
    </div>
</body>
</html>
