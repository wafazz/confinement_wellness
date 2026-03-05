<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #c8956c; padding-bottom: 15px; }
        .header h1 { color: #3d2c1e; font-size: 20px; margin: 0; }
        .header p { color: #8b6f5e; margin: 5px 0 0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px 0; }
        .info-table .label { color: #8b6f5e; width: 120px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background: #f8f0e8; color: #3d2c1e; padding: 8px 10px; text-align: left; border-bottom: 2px solid #c8956c; font-size: 11px; }
        table.data td { padding: 8px 10px; border-bottom: 1px solid #e8e0d8; font-size: 11px; }
        table.data tr:nth-child(even) { background: #fdf9f6; }
        .total-row { background: #f8f0e8 !important; font-weight: bold; }
        .total-row td { border-top: 2px solid #c8956c; }
        .section-title { color: #3d2c1e; margin-top: 25px; margin-bottom: 5px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #8b6f5e; }
        .summary-box { background: #f8f0e8; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>C&W System — Team Commission Summary</h1>
        <p>Leader Commission Report</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Leader:</td>
            <td><strong>{{ $user->name }}</strong></td>
            <td class="label">Month:</td>
            <td><strong>{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong></td>
        </tr>
        <tr>
            <td class="label">State:</td>
            <td>{{ $user->state ?? '-' }}</td>
            <td class="label">Generated:</td>
            <td>{{ now()->format('d M Y, h:i A') }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <strong>Summary:</strong>
        Own Override: <strong>RM {{ number_format($ownTotal, 2) }}</strong> |
        Team Direct: <strong>RM {{ number_format($teamTotal, 2) }}</strong> |
        Grand Total: <strong>RM {{ number_format($ownTotal + $teamTotal, 2) }}</strong>
    </div>

    <h3 class="section-title">Own Override Commission</h3>
    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>Job Code</th>
                <th>Therapist</th>
                <th>Service</th>
                <th style="text-align:right;">Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ownCommissions as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $c->serviceJob->job_code ?? '-' }}</td>
                <td>{{ $c->serviceJob->assignee->name ?? '-' }}</td>
                <td>{{ $c->serviceJob->service_type ?? '-' }}</td>
                <td style="text-align:right;">{{ number_format($c->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align:right;">Total Override:</td>
                <td style="text-align:right;">RM {{ number_format($ownTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <h3 class="section-title">Team Direct Commission</h3>
    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>Therapist</th>
                <th>Job Code</th>
                <th>Service</th>
                <th style="text-align:right;">Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teamCommissions as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $c->user->name ?? '-' }}</td>
                <td>{{ $c->serviceJob->job_code ?? '-' }}</td>
                <td>{{ $c->serviceJob->service_type ?? '-' }}</td>
                <td style="text-align:right;">{{ number_format($c->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align:right;">Total Direct:</td>
                <td style="text-align:right;">RM {{ number_format($teamTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated document. No signature required.</p>
        <p>Confinement & Women Wellness Agent Management System</p>
    </div>
</body>
</html>
