@extends('layouts.app')

@section('title', 'Commission')
@section('page-title', 'Commission')

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
                <a href="{{ route('leader.commissions.download-pdf', ['month' => $month]) }}" class="btn btn-sm" style="background:#c8956c; color:#fff;">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);border-radius:8px;">
                <div class="mb-1"><i class="fas fa-wallet fa-2x" style="color:#c8956c;"></i></div>
                <h4 class="mb-0" style="color:#3d2c1e;">RM {{ number_format($ownTotal, 2) }}</h4>
                <div class="small" style="color:#8b6f5e;">Your Override Commission</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);border-radius:8px;">
                <div class="mb-1"><i class="fas fa-clock fa-2x" style="color:#b07d58;"></i></div>
                <h4 class="mb-0" style="color:#3d2c1e;">RM {{ number_format($ownPending, 2) }}</h4>
                <div class="small" style="color:#8b6f5e;">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);border-radius:8px;">
                <div class="mb-1"><i class="fas fa-users fa-2x" style="color:#8b6f5e;"></i></div>
                <h4 class="mb-0" style="color:#3d2c1e;">RM {{ number_format($teamTotal, 2) }}</h4>
                <div class="small" style="color:#8b6f5e;">Team Total Commission</div>
            </div>
        </div>
    </div>
</div>

@if($affiliateTotal > 0)
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);border-radius:8px;">
                <div class="mb-1"><i class="fas fa-handshake fa-2x" style="color:#198754;"></i></div>
                <h4 class="mb-0" style="color:#3d2c1e;">RM {{ number_format($affiliateTotal, 2) }}</h4>
                <div class="small" style="color:#8b6f5e;">Affiliate Commission</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Own Override Commissions --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-wallet me-2" style="color:#c8956c;"></i>Your Override Commissions — {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h6>
    </div>
    <div class="card-body">
        @if($ownCommissions->isEmpty())
            <p class="text-muted text-center py-3">No override commissions for this month.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Job</th><th>Service</th><th>Therapist</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($ownCommissions as $c)
                    <tr>
                        <td>
                            <a href="{{ route('leader.jobs.show', $c->service_job_id) }}" class="text-decoration-none">
                                {{ $c->serviceJob->job_code ?? '-' }}
                            </a>
                        </td>
                        <td>{{ $c->serviceJob->service_type ?? '-' }}</td>
                        <td>{{ $c->serviceJob->assignee->name ?? '-' }}</td>
                        <td><strong>RM {{ number_format($c->amount, 2) }}</strong></td>
                        <td>
                            @php $sc = match($c->status) { 'pending' => 'warning', 'approved' => 'primary', 'paid' => 'success' }; @endphp
                            <span class="badge bg-{{ $sc }}">{{ ucfirst($c->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold"><td colspan="3">Total</td><td>RM {{ number_format($ownTotal, 2) }}</td><td></td></tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Affiliate Commissions --}}
@if($affiliateCommissions->isNotEmpty())
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-handshake me-2" style="color:#198754;"></i>Affiliate Commissions — {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Job</th><th>Service</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($affiliateCommissions as $c)
                    <tr>
                        <td>
                            <a href="{{ route('leader.jobs.show', $c->service_job_id) }}" class="text-decoration-none">
                                {{ $c->serviceJob->job_code ?? '-' }}
                            </a>
                        </td>
                        <td>{{ $c->serviceJob->service_type ?? '-' }}</td>
                        <td><strong>RM {{ number_format($c->amount, 2) }}</strong></td>
                        <td>
                            @php $sc = match($c->status) { 'pending' => 'warning', 'approved' => 'primary', 'paid' => 'success' }; @endphp
                            <span class="badge bg-{{ $sc }}">{{ ucfirst($c->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold"><td colspan="2">Total</td><td>RM {{ number_format($affiliateTotal, 2) }}</td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Team Direct Commissions --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-users me-2" style="color:#c8956c;"></i>Team Commissions — {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h6>
    </div>
    <div class="card-body">
        @if($teamCommissions->isEmpty())
            <p class="text-muted text-center py-3">No team commissions for this month.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Therapist</th><th>Job</th><th>Service</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($teamCommissions as $c)
                    <tr>
                        <td>{{ $c->user->name ?? '-' }}</td>
                        <td>{{ $c->serviceJob->job_code ?? '-' }}</td>
                        <td>{{ $c->serviceJob->service_type ?? '-' }}</td>
                        <td>RM {{ number_format($c->amount, 2) }}</td>
                        <td>
                            @php $sc = match($c->status) { 'pending' => 'warning', 'approved' => 'primary', 'paid' => 'success' }; @endphp
                            <span class="badge bg-{{ $sc }}">{{ ucfirst($c->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold"><td colspan="3">Team Total</td><td>RM {{ number_format($teamTotal, 2) }}</td><td></td></tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
