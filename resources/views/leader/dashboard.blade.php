@extends('layouts.app')

@section('title', 'Leader Dashboard')
@section('page-title', 'Leader Dashboard')

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
    .stat-card .stat-sub { font-size: 0.8rem; color: #a09080; }

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
            <i class="fas fa-user-tie"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold" style="color:#3d2c1e;">{{ $user->name }}</h4>
            <span style="color:#8b6f5e;">State Leader Therapist ({{ $user->state ?? 'N/A' }}) <i class="fas fa-info-circle fa-xs"></i></span>
            <br><small class="text-muted">Last login: {{ now()->format('d M, Y') }}</small>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row mt-3 g-3">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Team Size</div>
                <div class="stat-value">{{ $teamSize }}</div>
                <div class="stat-sub">therapists</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Jobs Completed</div>
                <div class="stat-value">{{ $totalJobsCompleted }}</div>
                <div class="stat-sub">{{ $totalJobsThisMonth }} this month</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Override Commission</div>
                <div class="stat-value">RM {{ number_format($directCommission, 0) }}</div>
                <div class="stat-sub">RM {{ number_format($directCommissionThisMonth, 0) }} this month</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Pending Jobs</div>
                <div class="stat-value">{{ $pendingJobsCount }}</div>
                <div class="stat-sub">to assign</div>
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

{{-- Active Jobs --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="section-title">Active Jobs</span>
        <a href="{{ route('leader.jobs.index') }}" class="btn btn-sm btn-outline-secondary">View All <i class="fas fa-chevron-right fa-xs"></i></a>
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
                        <th style="font-size:0.8rem; color:#8b7b6e;">Therapist</th>
                        <th style="font-size:0.8rem; color:#8b7b6e;">Status</th>
                        <th style="font-size:0.8rem; color:#8b7b6e;">Check-In Time</th>
                        <th style="font-size:0.8rem; color:#8b7b6e;">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeJobs as $job)
                    <tr>
                        <td class="ps-3">
                            <strong>{{ $job->job_code }}</strong><br>
                            <small class="text-muted">{{ $job->district }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="leaderboard-avatar" style="width:30px;height:30px;font-size:0.7rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <span style="font-size:0.85rem;">{{ $job->assignee->name ?? '-' }}</span><br>
                                    <small class="text-muted">{{ $job->service_type }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="job-status-badge status-{{ $job->status }}">{{ strtoupper($job->status) }}</span>
                        </td>
                        <td>
                            @if($job->checked_in_at)
                                {{ $job->checked_in_at->format('h:i A') }}
                            @else
                                <span class="text-muted">- Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($job->checked_in_at && $job->checked_out_at)
                                @php
                                    $diff = $job->checked_in_at->diff($job->checked_out_at);
                                @endphp
                                {{ $diff->h }}hr {{ $diff->i }}m
                            @elseif($job->checked_in_at)
                                @php
                                    $diff = $job->checked_in_at->diff(now());
                                @endphp
                                {{ $diff->h }}hr {{ $diff->i }}m
                            @else
                                -
                            @endif
                            <br><small class="text-muted">{{ $job->state }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Trainer Leaderboard + Recent Notifications --}}
<div class="row mb-4 g-3">
    {{-- Leaderboard --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="section-title">Trainer Leaderboard</span>
                <a href="{{ route('leader.therapists.index') }}" class="btn btn-sm btn-outline-secondary">View All <i class="fas fa-chevron-right fa-xs"></i></a>
            </div>
            <div class="card-body">
                @if($leaderboard->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-trophy fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">No data yet.</p>
                    </div>
                @else
                    @foreach($leaderboard as $i => $t)
                    <div class="leaderboard-item">
                        <div class="leaderboard-rank {{ $i < 3 ? 'rank-' . ($i + 1) : 'rank-default' }} me-3">
                            {{ $i + 1 }}
                        </div>
                        <div class="leaderboard-avatar me-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="font-size:0.9rem;">{{ $t->name }}</strong><br>
                            <small class="text-muted">RM {{ number_format($t->total_commission, 0) }} Commission</small>
                        </div>
                        <div class="text-end">
                            <strong style="color:#8b6f5e;">RM {{ number_format($t->total_commission, 0) }}</strong><br>
                            <small class="text-muted">{{ number_format($t->total_points, 0) }} pts</small>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Notifications --}}
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

{{-- Earnings & Performance + Points --}}
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
                <div class="row mt-3">
                    <div class="col-6">
                        <strong style="font-size:0.85rem;">Earnings Breakdown</strong>
                        <div class="mt-2">
                            <div class="earning-row">
                                <span><span class="earning-dot" style="background:#c8956c;"></span> Override Commission</span>
                                <strong>RM {{ number_format($downlineCommission, 0) }}</strong>
                            </div>
                            <div class="earning-row">
                                <span><span class="earning-dot" style="background:#e8d5c4;"></span> Team Direct</span>
                                <strong>RM {{ number_format($teamDirectCommission, 0) }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <strong style="font-size:0.85rem;">Total Earnings</strong>
                        <div class="mt-2">
                            <span style="font-size:1.5rem; font-weight:700; color:#3d2c1e;">
                                RM {{ number_format($downlineCommission + $teamDirectCommission, 0) }}
                            </span>
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
                <span class="section-title"><i class="fas fa-gift me-1" style="color:#c8956c;"></i> Points</span>
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
