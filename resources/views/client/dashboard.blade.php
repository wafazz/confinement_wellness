@extends('layouts.client')

@section('title', 'Dashboard — Client Portal')

@section('content')
<h4 class="fw-bold mb-1">{{ __('client.dashboard_welcome', ['name' => $client->name]) }}</h4>
<p class="text-muted mb-4">{{ __('client.dashboard_subtitle') }}</p>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.25rem;text-align:center;">
            <div style="font-size:1.8rem;font-weight:700;color:var(--warm-accent);">{{ $totalBookings }}</div>
            <div class="text-muted small">{{ __('client.dashboard_total_bookings') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.25rem;text-align:center;">
            <div style="font-size:1.8rem;font-weight:700;color:#fd7e14;">{{ $pendingBookings }}</div>
            <div class="text-muted small">{{ __('client.dashboard_pending_review') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.25rem;text-align:center;">
            <div style="font-size:1.8rem;font-weight:700;color:#0d6efd;">{{ $activeJobs }}</div>
            <div class="text-muted small">{{ __('client.dashboard_active_jobs') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.25rem;text-align:center;">
            <div style="font-size:1.8rem;font-weight:700;color:#198754;">{{ $completedJobs }}</div>
            <div class="text-muted small">{{ __('client.dashboard_completed') }}</div>
        </div>
    </div>
</div>

<!-- Reward Points -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.25rem;text-align:center;">
            <div style="font-size:1.8rem;font-weight:700;color:#c8956c;">{{ $rewardPoints }}</div>
            <div class="text-muted small">Reward Points</div>
        </div>
    </div>
</div>

<!-- Referral Link -->
@if($client->referral_code)
<div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.25rem;margin-bottom:1.5rem;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-1" style="color:var(--warm-text);"><i class="fas fa-gift me-2" style="color:var(--warm-accent);"></i>Your Referral Link</h6>
            <div class="text-muted small">Share this link with friends and earn reward points when they book!</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <input type="text" class="form-control form-control-sm" id="referralLink" readonly
                value="{{ url('/book?ref=' . $client->referral_code) }}" style="min-width:250px;">
            <button class="btn btn-sm btn-warm text-nowrap" onclick="navigator.clipboard.writeText(document.getElementById('referralLink').value);this.innerHTML='<i class=\'fas fa-check me-1\'></i>Copied!';setTimeout(()=>this.innerHTML='<i class=\'fas fa-copy me-1\'></i>Copy',2000);">
                <i class="fas fa-copy me-1"></i>Copy
            </button>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    <!-- Active Jobs -->
    <div class="col-lg-7">
        <div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.5rem;">
            <h6 class="fw-bold mb-3"><i class="fas fa-briefcase me-2" style="color:var(--warm-accent)"></i>{{ __('client.dashboard_active_jobs_title') }}</h6>
            @if($activeServiceJobs->count())
                @foreach($activeServiceJobs as $job)
                <div style="border:1px solid var(--warm-border);border-radius:8px;padding:1rem;margin-bottom:0.75rem;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-bold">{{ $job->service_type }}</div>
                            <div class="text-muted small">{{ $job->job_code }} &middot; {{ $job->job_date->format('d M Y') }}</div>
                            @if($job->assignee)
                                <div class="text-muted small"><i class="fas fa-user-nurse me-1"></i>{{ $job->assignee->name }}</div>
                            @endif
                        </div>
                        @php
                            $color = match($job->status) {
                                'pending' => 'secondary',
                                'accepted' => 'primary',
                                'checked_in' => 'warning',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ __('client.job_status_' . $job->status) }}</span>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-muted small mb-0">{{ __('client.dashboard_no_active_jobs') }}</p>
            @endif
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="col-lg-5">
        <div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.5rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-calendar-check me-2" style="color:var(--warm-accent)"></i>{{ __('client.dashboard_recent_bookings') }}</h6>
                <a href="{{ route('client.bookings.index') }}" class="small" style="color:var(--warm-accent);">{{ __('client.dashboard_view_all') }}</a>
            </div>
            @if($recentBookings->count())
                @foreach($recentBookings as $booking)
                <a href="{{ route('client.bookings.show', $booking) }}" class="d-block text-decoration-none" style="color:inherit;">
                    <div style="border:1px solid var(--warm-border);border-radius:8px;padding:0.75rem;margin-bottom:0.5rem;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small">{{ $booking->booking_code }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $booking->service_type }} &middot; {{ $booking->preferred_date->format('d M') }}</div>
                            </div>
                            @php
                                $bColor = match($booking->status) {
                                    'pending_review' => 'warning',
                                    'approved' => 'info',
                                    'converted' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $bColor }}" style="font-size:0.7rem;">{{ __('client.status_' . $booking->status) }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            @else
                <p class="text-muted small mb-0">{{ __('client.dashboard_no_bookings') }} <a href="{{ route('public.booking.create') }}" style="color:var(--warm-accent);">{{ __('client.dashboard_book_now') }}</a></p>
            @endif
        </div>
    </div>
</div>
@endsection
