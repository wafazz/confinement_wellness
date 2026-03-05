@extends('layouts.app')

@section('title', 'Therapist Dashboard')
@section('page-title', 'My Dashboard')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #f8f0e8 0%, #f5e6d8 100%);
        border-radius: 12px;
        padding: 1.5rem;
    }
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #e8d5c4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #8b6f5e;
    }
    .stat-card {
        border: 1px solid #e8e0d8;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        background: #fff;
    }
    .stat-card .stat-label { font-size: 0.8rem; color: #8b7b6e; }
    .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; color: #3d2c1e; }
    .stat-card .stat-sub { font-size: 0.75rem; color: #a09080; }

    .status-accepted { background: #d4edda; color: #155724; }
    .status-assigned, .status-pending { background: #fff3cd; color: #856404; }
    .status-checked_in { background: #f8d7da; color: #721c24; }
    .status-completed { background: #cce5ff; color: #004085; }

    .job-status-badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        text-transform: uppercase;
    }

    .leaderboard-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0ebe5;
    }
    .leaderboard-item:last-child { border-bottom: none; }
    .leaderboard-item.is-me { background: #fdf6f0; border-radius: 8px; padding: 0.75rem; margin: 0 -0.75rem; }
    .leaderboard-rank {
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
    .rank-default { background: #e8e0d8; color: #5a4800; }

    .leaderboard-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e8d5c4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        color: #8b6f5e;
        flex-shrink: 0;
    }

    .notif-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .points-bar-track {
        background: #e8e0d8;
        border-radius: 10px;
        height: 10px;
        overflow: hidden;
    }
    .points-bar-fill {
        background: linear-gradient(90deg, #c8956c, #8b6f5e);
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #3d2c1e;
    }

    .earning-row {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
        font-size: 0.85rem;
    }
    .earning-dot {
        width: 10px;
        height: 10px;
        border-radius: 2px;
        display: inline-block;
        margin-right: 6px;
    }
</style>
@endpush

@section('content')
{{-- Profile Header --}}
<div class="profile-header mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="profile-avatar">
            <i class="fas fa-hand-holding-medical"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold" style="color:#3d2c1e;">{{ $user->name }}</h4>
            <span style="color:#8b6f5e;">Therapist ({{ $user->state ?? 'N/A' }}) <i class="fas fa-info-circle fa-xs"></i></span>
            @if($user->leader)
                <br><small class="text-muted">Leader: {{ $user->leader->name }}</small>
            @endif
            <br><small class="text-muted">Last login: {{ now()->format('d M, Y') }}</small>
        </div>
        @if($myRank)
        <div class="ms-auto text-center d-none d-md-block">
            <div style="font-size:2rem; font-weight:700; color:#c8956c;">#{{ $myRank }}</div>
            <small class="text-muted">Ranking</small>
        </div>
        @endif
    </div>

    {{-- Stat Cards --}}
    <div class="row mt-3 g-3">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Jobs Completed</div>
                <div class="stat-value">{{ $totalJobsCompleted }}</div>
                <div class="stat-sub">{{ $jobsThisMonth }} this month</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Total Commission</div>
                <div class="stat-value">RM {{ number_format($totalCommission, 0) }}</div>
                <div class="stat-sub">RM {{ number_format($commissionThisMonth, 0) }} this month</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Total Points</div>
                <div class="stat-value">{{ number_format($totalPoints) }}</div>
                <div class="stat-sub">{{ number_format($pointsThisMonth) }} this month</div>
            </div>
        </div>
    </div>
</div>

{{-- Referral Link --}}
@if(auth()->user()->referral_code)
<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,#faf6f2,#f3ebe3);">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-1" style="color:#3d2c1e;"><i class="fas fa-link me-2" style="color:#c8956c;"></i>Your Referral Link</h6>
                <div class="text-muted small">Share this link to earn affiliate commission on bookings.</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" class="form-control form-control-sm" id="referralLink" readonly
                    value="{{ url('/book?ref=' . auth()->user()->referral_code) }}" style="min-width:280px;background:#fff;">
                <button class="btn btn-sm text-nowrap" style="background:#c8956c;color:#fff;" onclick="navigator.clipboard.writeText(document.getElementById('referralLink').value);this.innerHTML='<i class=\'fas fa-check me-1\'></i>Copied!';setTimeout(()=>this.innerHTML='<i class=\'fas fa-copy me-1\'></i>Copy',2000);">
                    <i class="fas fa-copy me-1"></i>Copy
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Active / Upcoming Jobs --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="section-title">My Active Jobs</span>
        <a href="{{ route('therapist.jobs.index') }}" class="btn btn-sm btn-outline-secondary">View All <i class="fas fa-chevron-right fa-xs"></i></a>
    </div>
    <div class="card-body p-0">
        @if($activeJobs->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-briefcase fa-2x mb-2 opacity-50"></i>
                <p class="mb-0">No active jobs at the moment.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#faf6f2;">
                    <tr>
                        <th class="ps-3" style="font-size:0.8rem; color:#8b7b6e;">Code & Location</th>
                        <th style="font-size:0.8rem; color:#8b7b6e;">Client</th>
                        <th style="font-size:0.8rem; color:#8b7b6e;">Service</th>
                        <th style="font-size:0.8rem; color:#8b7b6e;">Date / Time</th>
                        <th style="font-size:0.8rem; color:#8b7b6e;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeJobs as $job)
                    <tr>
                        <td class="ps-3">
                            <strong>{{ $job->job_code }}</strong><br>
                            <small class="text-muted">{{ $job->district }}, {{ $job->state }}</small>
                        </td>
                        <td>
                            {{ $job->client_name }}<br>
                            <small class="text-muted">{{ $job->client_phone }}</small>
                        </td>
                        <td>{{ $job->service_type }}</td>
                        <td>
                            {{ $job->job_date->format('d M Y') }}<br>
                            <small class="text-muted">{{ $job->job_time }}</small>
                        </td>
                        <td>
                            <span class="job-status-badge status-{{ $job->status }}">{{ strtoupper($job->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Leaderboard + Notifications --}}
<div class="row mb-4 g-3">
    {{-- Leaderboard --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="section-title">Leaderboard</span>
                @if($myRank)
                    <span class="badge" style="background:#c8956c; color:#fff;">Your Rank: #{{ $myRank }}</span>
                @endif
            </div>
            <div class="card-body">
                @if($topTherapists->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-trophy fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">No data yet.</p>
                    </div>
                @else
                    @foreach($topTherapists as $i => $t)
                    <div class="leaderboard-item {{ $t->id === $user->id ? 'is-me' : '' }}">
                        <div class="leaderboard-rank {{ $i < 3 ? 'rank-' . ($i + 1) : 'rank-default' }} me-3">
                            {{ $i + 1 }}
                        </div>
                        <div class="leaderboard-avatar me-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="font-size:0.9rem;">{{ $t->name }}</strong>
                            @if($t->id === $user->id) <small class="text-muted">(You)</small> @endif
                        </div>
                        <div class="text-end">
                            <strong style="color:#8b6f5e;">{{ number_format($t->total_points, 0) }} pts</strong>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="section-title">Recent Notifications</span>
            </div>
            <div class="card-body">
                @php
                    $notifications = auth()->user()->unreadNotifications->take(5);
                @endphp
                @if($notifications->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-bell fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">No notifications yet.</p>
                    </div>
                @else
                    @foreach($notifications as $notif)
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <div class="notif-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <span style="font-size:0.85rem;">{{ $notif->data['message'] ?? 'Notification' }}</span>
                            <br><small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Earnings Chart + Points Progress --}}
<div class="row mb-4 g-3">
    {{-- Earnings Chart --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="section-title">Earnings & Performance</span>
            </div>
            <div class="card-body">
                <canvas id="earningsChart" height="200"></canvas>

                <hr>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <small class="text-muted">Total Earned</small>
                        <div style="font-size:1.5rem; font-weight:700; color:#3d2c1e;">
                            RM {{ number_format($totalCommission, 0) }}
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">This Month</small>
                        <div style="font-size:1.25rem; font-weight:600; color:#c8956c;">
                            RM {{ number_format($commissionThisMonth, 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Points Progress --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="section-title"><i class="fas fa-gift me-1" style="color:#c8956c;"></i> Points & Rewards</span>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <span style="font-size:2.5rem; font-weight:700; color:#3d2c1e;">{{ number_format($totalPoints) }}</span>
                    <span style="font-size:1rem; color:#8b7b6e;"> Points</span>
                </div>

                @if($nextTier)
                    @php
                        $prevMin = $currentTier ? $currentTier->min_points : 0;
                        $range = $nextTier->min_points - $prevMin;
                        $progress = $range > 0 ? min(100, (($totalPoints - $prevMin) / $range) * 100) : 0;
                        $remaining = max(0, $nextTier->min_points - $totalPoints);
                    @endphp
                    <div class="points-bar-track mb-2">
                        <div class="points-bar-fill" style="width:{{ $progress }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $currentTier->title ?? 'Start' }}</small>
                        <small style="color:#8b6f5e; font-weight:600;">{{ $remaining }} points to {{ $nextTier->title }}</small>
                    </div>
                @else
                    <div class="text-center">
                        <span class="badge" style="background:#c8956c; color:#fff; font-size:0.85rem; padding:0.5rem 1rem;">
                            <i class="fas fa-crown me-1"></i> {{ $currentTier->title ?? 'Max Tier Reached' }}
                        </span>
                    </div>
                @endif

                <hr>
                <h6 style="color:#3d2c1e;">Current Tier</h6>
                @if($currentTier)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:#c8956c; color:#fff;">{{ $currentTier->title }}</span>
                        <small class="text-muted">{{ $currentTier->reward_description }}</small>
                    </div>
                @else
                    <p class="text-muted small">Complete jobs to earn points and unlock reward tiers!</p>
                @endif

                @if($nextTier)
                    <h6 class="mt-3" style="color:#3d2c1e;">Next Tier</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary">{{ $nextTier->title }}</span>
                        <small class="text-muted">{{ $nextTier->min_points }} pts — {{ $nextTier->reward_description }}</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('earningsChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Commission (RM)',
            data: @json($chartData),
            backgroundColor: 'rgba(200, 149, 108, 0.7)',
            borderColor: '#c8956c',
            borderWidth: 1,
            borderRadius: 6,
            barPercentage: 0.6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(v) { return 'RM ' + v; }
                },
                grid: { color: '#f0ebe5' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>
@endpush
