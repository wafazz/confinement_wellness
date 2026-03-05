@extends('layouts.app')

@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard')

@push('styles')
<style>
    .leaderboard-item {
        display: flex;
        align-items: center;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f0ebe5;
        transition: background 0.15s;
    }
    .leaderboard-item:hover { background: #fdf6f0; }
    .leaderboard-item:last-child { border-bottom: none; }
    .leaderboard-item.is-me { background: #fdf6f0; border-left: 3px solid #c8956c; }
    .rank-badge {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .rank-1 { background: #ffd700; color: #5a4800; }
    .rank-2 { background: #c0c0c0; color: #404040; }
    .rank-3 { background: #cd7f32; color: #fff; }
    .rank-default { background: #e8e0d8; color: #5a4800; }
    .filter-btn { border-radius: 20px; font-size: 0.85rem; padding: 0.4rem 1rem; }
    .filter-btn.active-filter { background: #c8956c; color: #fff; border-color: #c8956c; }
    .my-rank-card {
        background: linear-gradient(135deg, #f8f0e8, #f5e6d8);
        border-radius: 12px;
        padding: 1.25rem;
    }
</style>
@endpush

@section('content')
{{-- My Rank Card --}}
<div class="my-rank-card mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h5 class="mb-1 fw-bold" style="color:#3d2c1e;">Your Ranking</h5>
            <span style="color:#8b6f5e;">{{ ucfirst($filter) }} &middot; {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</span>
        </div>
        <div class="text-center">
            @if($myRank)
                <div style="font-size:2.5rem; font-weight:700; color:#c8956c;">#{{ $myRank }}</div>
                <div style="font-size:0.85rem; color:#8b6f5e;">{{ number_format($myPoints) }} pts</div>
            @else
                <div style="font-size:1.2rem; color:#94a3b8;">Not ranked</div>
            @endif
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted me-2" style="font-size:0.85rem;">Filter:</span>
        <a href="{{ route('therapist.leaderboard') }}?filter=nationwide&month={{ $month }}"
            class="btn btn-sm filter-btn {{ $filter === 'nationwide' ? 'active-filter' : 'btn-outline-secondary' }}">
            <i class="fas fa-globe me-1"></i> Nationwide
        </a>
        <a href="{{ route('therapist.leaderboard') }}?filter=state&month={{ $month }}"
            class="btn btn-sm filter-btn {{ $filter === 'state' ? 'active-filter' : 'btn-outline-secondary' }}">
            <i class="fas fa-map-marker-alt me-1"></i> {{ $user->state ?? 'State' }}
        </a>
        <a href="{{ route('therapist.leaderboard') }}?filter=team&month={{ $month }}"
            class="btn btn-sm filter-btn {{ $filter === 'team' ? 'active-filter' : 'btn-outline-secondary' }}">
            <i class="fas fa-users me-1"></i> My Team
        </a>
        <div class="ms-auto">
            <select id="monthSelector" class="form-select form-select-sm" style="width:180px;" onchange="changeMonth(this.value)">
                <option value="{{ now()->format('Y-m') }}" {{ $month === now()->format('Y-m') ? 'selected' : '' }}>This Month</option>
                @foreach($availableMonths as $m)
                    @if($m !== now()->format('Y-m'))
                    <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($m . '-01')->format('F Y') }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Leaderboard --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0" style="color:#3d2c1e;">
            <i class="fas fa-ranking-star me-1" style="color:#c8956c;"></i>
            {{ ucfirst($filter) }} Ranking
        </h6>
        <span class="text-muted" style="font-size:0.8rem;">{{ $rankings->count() }} therapists</span>
    </div>
    <div class="card-body p-0">
        @if($rankings->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-trophy fa-2x mb-2 opacity-50"></i>
                <p class="mb-0">No data for this period.</p>
            </div>
        @else
            @foreach($rankings as $i => $r)
            <div class="leaderboard-item {{ $r->id === $user->id ? 'is-me' : '' }}">
                <div class="rank-badge {{ $i < 3 ? 'rank-' . ($i + 1) : 'rank-default' }} me-3">
                    {{ $i + 1 }}
                </div>
                <div class="flex-grow-1">
                    <strong style="font-size:0.9rem;">{{ $r->name }}</strong>
                    @if($r->id === $user->id) <small style="color:#c8956c; font-weight:600;">(You)</small> @endif
                    <br><small class="text-muted">{{ $r->state ?? '-' }}</small>
                </div>
                <div class="text-end">
                    <strong style="color:#c8956c; font-size:1.1rem;">{{ number_format($r->total_points) }}</strong>
                    <br><small class="text-muted">points</small>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>

<script>
function changeMonth(month) {
    var url = new URL(window.location.href);
    url.searchParams.set('month', month);
    window.location.href = url.toString();
}
</script>
@endsection
