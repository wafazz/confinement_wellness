@extends('layouts.app')

@section('title', 'HQ Dashboard')
@section('page-title', 'HQ Dashboard')

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 12px;
        padding: 1.25rem;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        transition: transform 0.15s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; color: #1e293b; }
    .stat-card .stat-label { font-size: 0.8rem; color: #64748b; }
    .stat-card .stat-sub { font-size: 0.75rem; color: #94a3b8; }

    .section-title { font-size: 1rem; font-weight: 700; color: #1e293b; }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-top: 6px;
        flex-shrink: 0;
    }

    .rank-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    .rank-1 { background: #ffd700; color: #5a4800; }
    .rank-2 { background: #c0c0c0; color: #404040; }
    .rank-3 { background: #cd7f32; color: #fff; }
    .rank-default { background: #e2e8f0; color: #475569; }

    .pending-alert {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 1px solid #f59e0b;
        border-radius: 10px;
        padding: 1rem 1.25rem;
    }
</style>
@endpush

@section('content')
{{-- Welcome + Pending Alerts --}}
<div class="row mb-4 g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:#e0e7ff;">
                        <i class="fas fa-building" style="color:#4f46e5;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Welcome, {{ Auth::user()->name }}!</h5>
                        <small class="text-muted">{{ now()->format('l, d F Y') }} &middot; HQ Admin</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @if($pendingJobs > 0 || $pendingCommissions > 0 || $pendingUsers > 0 || $pendingBookings > 0)
        <div class="pending-alert h-100 d-flex align-items-center">
            <div>
                <strong style="color:#92400e;"><i class="fas fa-exclamation-triangle me-1"></i> Pending Actions</strong>
                <div class="mt-1" style="font-size:0.85rem; color:#78350f;">
                    @if($pendingBookings > 0)<span class="me-3"><i class="fas fa-calendar-check me-1"></i> {{ $pendingBookings }} bookings</span>@endif
                    @if($pendingJobs > 0)<span class="me-3"><i class="fas fa-briefcase me-1"></i> {{ $pendingJobs }} jobs</span>@endif
                    @if($pendingCommissions > 0)<span class="me-3"><i class="fas fa-money-bill me-1"></i> {{ $pendingCommissions }} commissions</span>@endif
                    @if($pendingUsers > 0)<span><i class="fas fa-user-clock me-1"></i> {{ $pendingUsers }} users</span>@endif
                </div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-center">
                <span class="text-muted"><i class="fas fa-check-circle text-success me-2"></i>All caught up!</span>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Stat Cards --}}
<div class="row mb-4 g-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Leaders</div>
                    <div class="stat-value">{{ $totalLeaders }}</div>
                </div>
                <div class="stat-icon" style="background:#e0e7ff; color:#4f46e5;">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Therapists</div>
                    <div class="stat-value">{{ $totalTherapists }}</div>
                    <div class="stat-sub">{{ $activeAgents }} active agents</div>
                </div>
                <div class="stat-icon" style="background:#dcfce7; color:#16a34a;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Jobs This Month</div>
                    <div class="stat-value">{{ $totalJobsThisMonth }}</div>
                    <div class="stat-sub">{{ $completedJobsThisMonth }} completed &middot; {{ $totalJobsAllTime }} all time</div>
                </div>
                <div class="stat-icon" style="background:#fef3c7; color:#d97706;">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Commission (Month)</div>
                    <div class="stat-value">RM {{ number_format($totalCommissionThisMonth, 0) }}</div>
                    <div class="stat-sub">RM {{ number_format($totalCommissionPaid, 0) }} paid &middot; RM {{ number_format($totalCommissionPending, 0) }} pending</div>
                </div>
                <div class="stat-icon" style="background:#fce7f3; color:#db2777;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row mb-4 g-3">
    {{-- Jobs by Status (Pie) --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <span class="section-title">Jobs by Status</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="statusChart" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- Jobs by State (Bar) --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <span class="section-title">Jobs by State</span>
            </div>
            <div class="card-body">
                <canvas id="stateChart" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- Monthly Trend (Line) --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <span class="section-title">Monthly Completed Jobs</span>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Top 10 Therapists + Recent Activity --}}
<div class="row mb-4 g-3">
    {{-- Top 10 --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-0">
                <span class="section-title"><i class="fas fa-trophy text-warning me-1"></i> Top 10 Therapists</span>
                <span class="text-muted" style="font-size:0.75rem;">By jobs completed</span>
            </div>
            <div class="card-body p-0">
                @if($topTherapists->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-trophy fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">No data yet.</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="ps-3" style="font-size:0.75rem; color:#64748b; width:50px;">#</th>
                                <th style="font-size:0.75rem; color:#64748b;">Therapist</th>
                                <th class="text-center" style="font-size:0.75rem; color:#64748b;">Jobs</th>
                                <th class="text-end" style="font-size:0.75rem; color:#64748b;">Commission</th>
                                <th class="text-end pe-3" style="font-size:0.75rem; color:#64748b;">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topTherapists as $i => $t)
                            <tr>
                                <td class="ps-3">
                                    <div class="rank-badge {{ $i < 3 ? 'rank-' . ($i + 1) : 'rank-default' }}">{{ $i + 1 }}</div>
                                </td>
                                <td>
                                    <strong style="font-size:0.85rem;">{{ $t->name }}</strong><br>
                                    <small class="text-muted">{{ $t->state ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $t->completed_jobs }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong style="color:#16a34a;">RM {{ number_format($t->total_commission ?? 0, 0) }}</strong>
                                </td>
                                <td class="text-end pe-3">
                                    <span style="color:#64748b;">{{ number_format($t->total_points ?? 0, 0) }} pts</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <span class="section-title"><i class="fas fa-clock text-info me-1"></i> Recent Activity</span>
            </div>
            <div class="card-body">
                @if($recentActivity->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-clock fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">No activity yet.</p>
                    </div>
                @else
                    @foreach($recentActivity as $job)
                    <div class="activity-item">
                        <div class="activity-dot" style="background:#198754;"></div>
                        <div class="flex-grow-1">
                            <div style="font-size:0.85rem;">
                                <strong>{{ $job->assignee->name ?? '-' }}</strong> completed
                                <span class="text-muted">{{ $job->service_type }}</span>
                            </div>
                            <small class="text-muted">
                                {{ $job->job_code }} &middot; {{ $job->district }}, {{ $job->state }}
                                @if($job->completed_at)
                                    &middot; {{ $job->completed_at->diffForHumans() }}
                                @endif
                            </small>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $statusDisplayLabels = array_map(fn($s) => ucfirst(str_replace('_', ' ', $s)), $statusLabels);
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Jobs by Status — Pie
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: @json($statusDisplayLabels),
        datasets: [{
            data: @json($statusData),
            backgroundColor: @json($statusColors),
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { boxWidth: 12, padding: 12, font: { size: 11 } }
            }
        }
    }
});

// Jobs by State — Bar
new Chart(document.getElementById('stateChart'), {
    type: 'bar',
    data: {
        labels: @json($stateLabels),
        datasets: [{
            label: 'Jobs',
            data: @json($stateData),
            backgroundColor: 'rgba(79, 70, 229, 0.7)',
            borderColor: '#4f46e5',
            borderWidth: 1,
            borderRadius: 6,
            barPercentage: 0.7
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            y: { grid: { display: false } }
        }
    }
});

// Monthly Trend — Line
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: @json($monthlyLabels),
        datasets: [{
            label: 'Completed Jobs',
            data: @json($monthlyJobs),
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#16a34a',
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
