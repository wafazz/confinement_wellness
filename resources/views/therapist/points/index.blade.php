@extends('layouts.app')

@section('title', 'Points & Rewards')
@section('page-title', 'Points & Rewards')

@push('styles')
<style>
    .tier-card { border-radius: 12px; padding: 1rem; text-align: center; }
    .tier-card.current { background: linear-gradient(135deg, #c8956c, #b07d58); color: #fff; }
    .tier-card.next { background: #f8f0e8; color: #3d2c1e; border: 2px dashed #c8956c; }
    .tier-card.locked { background: #f1f5f9; color: #94a3b8; }
    .points-big { font-size: 2.5rem; font-weight: 700; color: #3d2c1e; }
    .points-bar-track { background: #e8e0d8; border-radius: 10px; height: 12px; overflow: hidden; }
    .points-bar-fill { background: linear-gradient(90deg, #c8956c, #8b6f5e); height: 100%; border-radius: 10px; }
    .month-label { font-size: 0.75rem; color: #8b7b6e; }
    .month-bar { background: #e8e0d8; border-radius: 4px; height: 6px; overflow: hidden; margin-top: 4px; }
    .month-bar-fill { background: #c8956c; height: 100%; border-radius: 4px; }
    .point-record { padding: 0.75rem 0; border-bottom: 1px solid #f0ebe5; }
    .point-record:last-child { border-bottom: none; }
</style>
@endpush

@section('content')
{{-- Summary Cards --}}
<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="points-big">{{ number_format($totalPoints) }}</div>
                <div class="text-muted">Lifetime Points</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="points-big" style="color:#c8956c;">{{ number_format($pointsThisMonth) }}</div>
                <div class="text-muted">This Month</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                @if($currentTier)
                    <div style="font-size:1.5rem; font-weight:700; color:#c8956c;">
                        <i class="fas fa-medal me-1"></i> {{ $currentTier->title }}
                    </div>
                    <div class="text-muted">Current Tier</div>
                @else
                    <div style="font-size:1.5rem; font-weight:700; color:#94a3b8;">No Tier</div>
                    <div class="text-muted">Complete jobs to earn points</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Progress Bar --}}
@if($nextTier)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        @php
            $prevMin = $currentTier ? $currentTier->min_points : 0;
            $range = $nextTier->min_points - $prevMin;
            $progress = $range > 0 ? min(100, (($totalPoints - $prevMin) / $range) * 100) : 0;
            $remaining = max(0, $nextTier->min_points - $totalPoints);
        @endphp
        <div class="d-flex justify-content-between mb-2">
            <strong style="color:#3d2c1e;">{{ $currentTier->title ?? 'Start' }}</strong>
            <strong style="color:#c8956c;">{{ $nextTier->title }} ({{ $nextTier->min_points }} pts)</strong>
        </div>
        <div class="points-bar-track">
            <div class="points-bar-fill" style="width:{{ $progress }}%"></div>
        </div>
        <div class="text-center mt-2">
            <small style="color:#8b6f5e;">{{ $remaining }} points to {{ $nextTier->title }}</small>
        </div>
    </div>
</div>
@endif

{{-- Reward Tiers Overview --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h6 class="mb-0" style="color:#3d2c1e;"><i class="fas fa-trophy me-1" style="color:#c8956c;"></i> Reward Tiers</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($allTiers as $tier)
            @php
                $isCurrent = $currentTier && $tier->id === $currentTier->id;
                $isUnlocked = $totalPoints >= $tier->min_points;
            @endphp
            <div class="col-md-4">
                <div class="tier-card {{ $isCurrent ? 'current' : ($isUnlocked ? 'next' : 'locked') }}">
                    <div style="font-size:1.25rem; font-weight:700;">
                        @if($isCurrent) <i class="fas fa-crown me-1"></i> @endif
                        {{ $tier->title }}
                    </div>
                    <div style="font-size:0.85rem;">{{ $tier->min_points }} pts</div>
                    <div style="font-size:0.8rem; margin-top:0.5rem;">{{ $tier->reward_description }}</div>
                    @if($isCurrent)
                        <span class="badge bg-light text-dark mt-2">Current</span>
                    @elseif($isUnlocked)
                        <span class="badge bg-success mt-2">Unlocked</span>
                    @else
                        <span class="badge bg-secondary mt-2">Locked</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Monthly Breakdown --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0" style="color:#3d2c1e;">Monthly Summary</h6>
            </div>
            <div class="card-body">
                @php $maxPts = max(1, max($monthlyBreakdown)); @endphp
                @foreach($monthlyBreakdown as $m => $pts)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="month-label">{{ \Carbon\Carbon::parse($m . '-01')->format('M Y') }}</span>
                        <span class="month-label fw-bold">{{ $pts }} pts</span>
                    </div>
                    <div class="month-bar">
                        <div class="month-bar-fill" style="width:{{ ($pts / $maxPts) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Job-by-Job Breakdown --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0" style="color:#3d2c1e;">Points History</h6>
            </div>
            <div class="card-body">
                @if($pointRecords->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-star fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">No points earned yet.</p>
                    </div>
                @else
                    @foreach($pointRecords as $record)
                    <div class="point-record d-flex justify-content-between align-items-center">
                        <div>
                            <strong style="font-size:0.85rem;">{{ $record->serviceJob->job_code ?? '-' }}</strong>
                            <span class="text-muted ms-2" style="font-size:0.8rem;">{{ $record->serviceJob->service_type ?? '-' }}</span>
                            <br><small class="text-muted">{{ $record->created_at->format('d M Y') }}</small>
                        </div>
                        <div>
                            <span class="badge" style="background:#c8956c; color:#fff; font-size:0.85rem;">+{{ $record->points }} pts</span>
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-3">{{ $pointRecords->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
