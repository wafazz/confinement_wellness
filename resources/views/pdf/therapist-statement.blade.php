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
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #8b6f5e; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-approved { background: #cce5ff; color: #004085; }
        .badge-paid { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="header">
        <h1>C&W System — Commission Statement</h1>
        <p>Monthly Commission Report</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Name:</td>
            <td><strong>{{ $user->name }}</strong></td>
            <td class="label">Month:</td>
            <td><strong>{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Role:</td>
            <td>Therapist</td>
            <td class="label">State:</td>
            <td>{{ $user->state ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Generated:</td>
            <td>{{ now()->format('d M Y, h:i A') }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <h3 style="color:#3d2c1e; margin-top:20px;">Commission Details</h3>

    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>Job Code</th>
                <th>Service Type</th>
                <th>Type</th>
                <th>Date</th>
                <th>Status</th>
                <th style="text-align:right;">Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commissions as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $c->serviceJob->job_code ?? '-' }}</td>
                <td>{{ $c->serviceJob->service_type ?? '-' }}</td>
                <td>{{ ucfirst($c->type) }}</td>
                <td>{{ $c->serviceJob->completed_at ? $c->serviceJob->completed_at->format('d M Y') : '-' }}</td>
                <td>
                    <span class="badge badge-{{ $c->status }}">{{ ucfirst($c->status) }}</span>
                </td>
                <td style="text-align:right;">{{ number_format($c->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" style="text-align:right;"><strong>Total:</strong></td>
                <td style="text-align:right;"><strong>RM {{ number_format($total, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated document. No signature required.</p>
        <p>Confinement & Women Wellness Agent Management System</p>
    </div>
</body>
</html>
