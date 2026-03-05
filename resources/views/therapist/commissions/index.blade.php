@extends('layouts.app')

@section('title', 'My Commission')
@section('page-title', 'My Commission')

@section('content')
{{-- Month Selector --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small" style="color:#8b6f5e;">Select Month</label>
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($months as $m)
                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($m . '-01')->format('F Y') }}</option>
                    @endforeach
                    @if($months->isEmpty())
                        <option value="{{ now()->format('Y-m') }}">{{ now()->format('F Y') }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('therapist.commissions.download-pdf', ['month' => $month]) }}" class="btn btn-sm" style="background:#c8956c; color:#fff;">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-3">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);border-radius:8px;">
                <div class="small" style="color:#8b6f5e;">This Month</div>
                <h5 class="mb-0" style="color:#3d2c1e;">RM {{ number_format($totalEarned, 2) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);border-radius:8px;">
                <div class="small" style="color:#8b6f5e;">Pending</div>
                <h5 class="mb-0 text-warning">RM {{ number_format($totalPending, 2) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);border-radius:8px;">
                <div class="small" style="color:#8b6f5e;">Approved</div>
                <h5 class="mb-0 text-primary">RM {{ number_format($totalApproved, 2) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);border-radius:8px;">
                <div class="small" style="color:#8b6f5e;">Paid</div>
                <h5 class="mb-0 text-success">RM {{ number_format($totalPaid, 2) }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- All-Time Summary --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-6">
                <div class="small" style="color:#8b6f5e;">All-Time Earned</div>
                <h5 style="color:#c8956c;">RM {{ number_format($allTimeTotal, 2) }}</h5>
            </div>
            <div class="col-6">
                <div class="small" style="color:#8b6f5e;">All-Time Paid</div>
                <h5 style="color:#198754;">RM {{ number_format($allTimePaid, 2) }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Commission List --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-wallet me-2" style="color:#c8956c;"></i>Commission Breakdown — {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h6>
    </div>
    <div class="card-body">
        @if($commissions->isEmpty())
            <div class="text-center py-4">
                <i class="fas fa-coins fa-3x mb-3" style="color:#d5c8bc;"></i>
                <p class="text-muted">No commissions for this month yet.</p>
            </div>
        @else
            @foreach($commissions as $c)
            @php $sc = match($c->status) { 'pending' => 'warning', 'approved' => 'primary', 'paid' => 'success' }; @endphp
            <div class="d-flex justify-content-between align-items-center p-3 rounded mb-2" style="background:#faf6f2;">
                <div>
                    <div class="fw-bold" style="color:#3d2c1e;">
                        {{ $c->serviceJob->service_type ?? '-' }}
                        @if($c->type === 'affiliate')
                            <span class="badge bg-success ms-1" style="font-size:0.65rem;">Affiliate</span>
                        @endif
                    </div>
                    <div class="small text-muted">
                        <a href="{{ route('therapist.jobs.show', $c->service_job_id) }}" class="text-decoration-none">{{ $c->serviceJob->job_code ?? '-' }}</a>
                        — {{ $c->serviceJob->client_name ?? '' }}
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold" style="color:#c8956c;">RM {{ number_format($c->amount, 2) }}</div>
                    <span class="badge bg-{{ $sc }}">{{ ucfirst($c->status) }}</span>
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-between align-items-center p-3 rounded mt-3" style="background:linear-gradient(135deg,#c8956c,#b07d58);color:#fff;border-radius:8px;">
                <strong>Total This Month</strong>
                <h5 class="mb-0">RM {{ number_format($totalEarned, 2) }}</h5>
            </div>
        @endif
    </div>
</div>
@endsection
