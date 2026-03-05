@extends('layouts.app')
@section('title', 'Reports — C&W System')
@section('page-title', 'Reports')

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        transition: transform 0.15s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .period-group .btn { font-size: 0.85rem; }
    .period-group .btn-check:checked + .btn-outline-primary {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
    }
    .breakdown-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .breakdown-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .table-rank th { font-size: 0.8rem; text-transform: uppercase; color: #64748b; }
    .table-rank td { font-size: 0.875rem; }
    .date-range-badge { font-size: 0.8rem; }
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <h4 class="fw-bold mb-0">Reports</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('hq.reports.download-pdf', ['period' => $period, 'date' => $dateInput]) }}" class="btn btn-sm btn-danger">
            <i class="fas fa-file-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('hq.reports.download-csv', ['period' => $period, 'date' => $dateInput]) }}" class="btn btn-sm btn-success">
            <i class="fas fa-file-csv me-1"></i> CSV
        </a>
    </div>
</div>

{{-- Period Selector --}}
<form id="reportForm" method="GET" action="{{ route('hq.reports.index') }}">
    <div class="card mb-4 border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body py-3">
            <div class="row align-items-center g-3">
                <div class="col-auto">
                    <div class="btn-group period-group" role="group">
                        @foreach(['daily','weekly','monthly','yearly'] as $p)
                        <input type="radio" class="btn-check" name="period" id="period_{{ $p }}" value="{{ $p }}" {{ $period === $p ? 'checked' : '' }} onchange="switchPeriod()">
                        <label class="btn btn-outline-primary" for="period_{{ $p }}">{{ ucfirst($p) }}</label>
                        @endforeach
                    </div>
                </div>
                <div class="col-auto" id="datePickerWrap">
                    @if($period === 'daily')
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ $dateInput }}" onchange="this.form.submit();">
                    @elseif($period === 'weekly')
                        <input type="week" name="date" class="form-control form-control-sm" value="{{ $dateInput }}" onchange="this.form.submit();">
                    @elseif($period === 'monthly')
                        <input type="month" name="date" class="form-control form-control-sm" value="{{ $dateInput }}" onchange="this.form.submit();">
                    @else
                        <select name="date" class="form-select form-select-sm" onchange="this.form.submit();">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $dateInput == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    @endif
                </div>
                <div class="col-auto">
                    <span class="badge bg-light text-dark date-range-badge">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $startDate->format('d M Y') }} &mdash; {{ $endDate->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-briefcase"></i></div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;">Total Jobs</div>
                    <div class="fw-bold fs-5">{{ number_format($totalJobs) }}</div>
                    <small class="text-muted">{{ $completedJobs }} completed &middot; {{ $pendingJobs }} pending</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;">Total Commission</div>
                    <div class="fw-bold fs-5">RM {{ number_format($totalCommission, 2) }}</div>
                    <small class="text-muted">D: {{ number_format($directCommission, 2) }} &middot; O: {{ number_format($overrideCommission, 2) }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;">Bookings</div>
                    <div class="fw-bold fs-5">{{ number_format($totalBookings) }}</div>
                    <small class="text-muted">{{ $convertedBookings }} converted &middot; {{ $pendingBookings }} pending</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-users"></i></div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;">Active Agents</div>
                    <div class="fw-bold fs-5">{{ $activeLeaders + $activeTherapists }}</div>
                    <small class="text-muted">{{ $activeLeaders }} leaders &middot; {{ $activeTherapists }} therapists</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Breakdowns Row --}}
<div class="row g-3 mb-4">
    {{-- Jobs by Service Type --}}
    <div class="col-lg-4">
        <div class="card breakdown-card h-100">
            <div class="card-header"><i class="fas fa-tags me-2 text-primary"></i>Jobs by Service Type</div>
            <div class="card-body p-0">
                @if($jobsByServiceType->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">No data</p>
                @else
                <table class="table table-sm mb-0">
                    <thead><tr class="table-rank"><th>Service Type</th><th class="text-end">Count</th></tr></thead>
                    <tbody>
                        @foreach($jobsByServiceType as $row)
                        <tr><td>{{ $row->service_type }}</td><td class="text-end fw-semibold">{{ $row->total }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Jobs by State --}}
    <div class="col-lg-4">
        <div class="card breakdown-card h-100">
            <div class="card-header"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Jobs by State</div>
            <div class="card-body p-0">
                @if($jobsByState->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">No data</p>
                @else
                <table class="table table-sm mb-0">
                    <thead><tr class="table-rank"><th>State</th><th class="text-end">Count</th></tr></thead>
                    <tbody>
                        @foreach($jobsByState as $row)
                        <tr><td>{{ $row->state }}</td><td class="text-end fw-semibold">{{ $row->total }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Commission by Type (Doughnut) --}}
    <div class="col-lg-4">
        <div class="card breakdown-card h-100">
            <div class="card-header"><i class="fas fa-chart-pie me-2 text-success"></i>Commission by Type</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if($totalCommission > 0)
                    <canvas id="commDoughnut" style="max-height:220px;"></canvas>
                @else
                    <p class="text-muted mb-0">No commission data</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Top Performers --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card breakdown-card">
            <div class="card-header"><i class="fas fa-trophy me-2 text-warning"></i>Top 5 Therapists</div>
            <div class="card-body p-0">
                @if($topTherapists->isEmpty() || $topTherapists->sum('jobs_count') == 0)
                    <p class="text-muted text-center py-4 mb-0">No data</p>
                @else
                <table class="table table-sm mb-0">
                    <thead><tr class="table-rank"><th>#</th><th>Name</th><th class="text-end">Jobs</th><th class="text-end">Commission (RM)</th></tr></thead>
                    <tbody>
                        @foreach($topTherapists as $i => $t)
                        @if($t->jobs_count > 0)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $t->name }}</td>
                            <td class="text-end">{{ $t->jobs_count }}</td>
                            <td class="text-end">{{ number_format($t->commission_total ?? 0, 2) }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card breakdown-card">
            <div class="card-header"><i class="fas fa-user-tie me-2 text-info"></i>Top 5 Leaders</div>
            <div class="card-body p-0">
                @if($topLeaders->isEmpty() || $topLeaders->sum('jobs_count') == 0)
                    <p class="text-muted text-center py-4 mb-0">No data</p>
                @else
                <table class="table table-sm mb-0">
                    <thead><tr class="table-rank"><th>#</th><th>Name</th><th class="text-end">Team Jobs</th><th class="text-end">Override (RM)</th></tr></thead>
                    <tbody>
                        @foreach($topLeaders as $i => $l)
                        @if($l->jobs_count > 0)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $l->name }}</td>
                            <td class="text-end">{{ $l->jobs_count }}</td>
                            <td class="text-end">{{ number_format($l->commission_total ?? 0, 2) }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
@if($totalCommission > 0)
new Chart(document.getElementById('commDoughnut'), {
    type: 'doughnut',
    data: {
        labels: ['Direct', 'Override', 'Affiliate'],
        datasets: [{
            data: [{{ $directCommission }}, {{ $overrideCommission }}, {{ $affiliateCommission }}],
            backgroundColor: ['#4f46e5', '#0ea5e9', '#22c55e'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
        }
    }
});
@endif

function switchPeriod() {
    var dateEl = document.querySelector('#datePickerWrap [name="date"]');
    if (dateEl) dateEl.remove();
    document.getElementById('reportForm').submit();
}
</script>
@endpush
