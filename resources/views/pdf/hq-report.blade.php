<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e293b; padding-bottom: 15px; }
        .header h1 { color: #1e293b; font-size: 20px; margin: 0; }
        .header p { color: #64748b; margin: 5px 0 0; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background: #1e293b; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
        table.data td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        table.data tr:nth-child(even) { background: #f8fafc; }
        .total-row { background: #f1f5f9 !important; font-weight: bold; }
        .total-row td { border-top: 2px solid #1e293b; }
        .summary-box { background: #f1f5f9; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #64748b; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; }
        .badge-direct { background: #dbeafe; color: #1d4ed8; }
        .badge-override { background: #e0f2fe; color: #0369a1; }
        .badge-affiliate { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="header">
        <h1>C&W System — HQ Commission Report</h1>
        <p>{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }} &mdash; Generated {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="summary-box">
        <strong>Summary:</strong>
        Direct Commissions: <strong>RM {{ number_format($totalDirect, 2) }}</strong> |
        Override Commissions: <strong>RM {{ number_format($totalOverride, 2) }}</strong> |
        Affiliate Commissions: <strong>RM {{ number_format($totalAffiliate, 2) }}</strong> |
        Grand Total: <strong>RM {{ number_format($grandTotal, 2) }}</strong> |
        Records: <strong>{{ $commissions->count() }}</strong>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Role</th>
                <th>Type</th>
                <th>Job Code</th>
                <th>Service</th>
                <th>Status</th>
                <th style="text-align:right;">Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commissions as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $c->user->name ?? '-' }}</td>
                <td>{{ ucfirst($c->user->role ?? '-') }}</td>
                <td><span class="badge badge-{{ $c->type }}">{{ ucfirst($c->type) }}</span></td>
                <td>{{ $c->serviceJob->job_code ?? '-' }}</td>
                <td>{{ $c->serviceJob->service_type ?? '-' }}</td>
                <td>{{ ucfirst($c->status) }}</td>
                <td style="text-align:right;">{{ number_format($c->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" style="text-align:right;">Grand Total:</td>
                <td style="text-align:right;">RM {{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This is a system-generated document. No signature required.</p>
        <p>Confinement & Women Wellness Agent Management System</p>
    </div>
</body>
</html>
