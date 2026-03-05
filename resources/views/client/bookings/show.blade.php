@extends('layouts.client')

@section('title', 'Booking ' . $booking->booking_code)

@section('content')
<a href="{{ route('client.bookings.index') }}" class="text-decoration-none small" style="color:var(--warm-accent);">
    <i class="fas fa-arrow-left me-1"></i>{{ __('client.show_back') }}
</a>

<div class="mt-3" style="background:#fff;border-radius:12px;border:1px solid var(--warm-border);padding:2rem;">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h5 class="fw-bold mb-1">{{ $booking->booking_code }}</h5>
            <p class="text-muted mb-0">{{ $booking->service_type }}</p>
        </div>
        @php
            $color = match($booking->status) {
                'pending_review' => 'warning',
                'approved' => 'info',
                'converted' => 'success',
                'rejected' => 'danger',
                default => 'secondary',
            };
        @endphp
        <span class="badge bg-{{ $color }} fs-6">{{ __('client.status_' . $booking->status) }}</span>
    </div>

    <!-- Status Timeline -->
    <div class="mb-4 p-3" style="background:var(--warm-bg);border-radius:8px;">
        <div class="d-flex justify-content-between text-center">
            @php
                $steps = ['pending_review' => __('client.timeline_submitted'), 'approved' => __('client.timeline_approved'), 'converted' => __('client.timeline_job_created')];
                $statusOrder = ['pending_review', 'approved', 'converted'];
                $currentIdx = array_search($booking->status, $statusOrder);
                if ($currentIdx === false) $currentIdx = -1;
                if ($booking->status === 'rejected') $currentIdx = -2;
            @endphp
            @foreach($steps as $key => $label)
                @php
                    $idx = array_search($key, $statusOrder);
                    $done = $idx <= $currentIdx;
                @endphp
                <div class="flex-fill">
                    <div style="width:30px;height:30px;border-radius:50%;margin:0 auto 0.5rem;display:flex;align-items:center;justify-content:center;
                        background:{{ $done ? 'var(--warm-accent)' : '#e8ddd3' }};color:{{ $done ? '#fff' : 'var(--warm-muted)' }};font-size:0.8rem;font-weight:700;">
                        @if($done) <i class="fas fa-check"></i> @else {{ $idx + 1 }} @endif
                    </div>
                    <div class="small {{ $done ? 'fw-bold' : 'text-muted' }}">{{ $label }}</div>
                </div>
            @endforeach
        </div>
        @if($booking->status === 'rejected')
            <div class="alert alert-danger mt-3 mb-0 small">
                <i class="fas fa-times-circle me-1"></i>{{ __('client.show_rejected') }}
                @if($booking->admin_notes) <br>{{ __('client.show_rejected_reason') }} {{ $booking->admin_notes }} @endif
            </div>
        @endif
    </div>

    <!-- Details -->
    <div class="row g-4">
        <div class="col-md-6">
            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2" style="color:var(--warm-accent)"></i>{{ __('client.show_booking_details') }}</h6>
            <table class="table table-sm table-borderless">
                <tr><td class="text-muted" style="width:40%">{{ __('client.show_date') }}</td><td>{{ $booking->preferred_date->format('d M Y') }}</td></tr>
                <tr><td class="text-muted">{{ __('client.show_time') }}</td><td>{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }}</td></tr>
                <tr><td class="text-muted">{{ __('client.show_location') }}</td><td>{{ $booking->district }}, {{ $booking->state }}</td></tr>
                <tr><td class="text-muted">{{ __('client.show_address') }}</td><td>{{ $booking->client_address }}</td></tr>
                @if($booking->preferredTherapist)
                    <tr><td class="text-muted">{{ __('client.show_preferred_therapist') }}</td><td>{{ $booking->preferredTherapist->name }}</td></tr>
                @endif
                @if($booking->notes)
                    <tr><td class="text-muted">{{ __('client.show_notes') }}</td><td>{{ $booking->notes }}</td></tr>
                @endif
                <tr><td class="text-muted">{{ __('client.show_submitted') }}</td><td>{{ $booking->created_at->format('d M Y, g:i A') }}</td></tr>
                <tr><td class="text-muted">{{ __('client.show_source') }}</td><td><span class="badge bg-{{ $booking->source === 'registered' ? 'primary' : 'secondary' }}">{{ ucfirst($booking->source) }}</span></td></tr>
                @if($booking->referral_code)
                    <tr><td class="text-muted">Referral Code</td><td><span class="badge bg-success">{{ $booking->referral_code }}</span></td></tr>
                @endif
            </table>
        </div>

        <!-- Linked Job -->
        <div class="col-md-6">
            @if($booking->serviceJob)
                <h6 class="fw-bold mb-3"><i class="fas fa-briefcase me-2" style="color:var(--warm-accent)"></i>{{ __('client.show_assigned_job') }}</h6>
                <div style="border:1px solid var(--warm-border);border-radius:8px;padding:1rem;">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width:40%">{{ __('client.show_job_code') }}</td><td class="fw-bold">{{ $booking->serviceJob->job_code }}</td></tr>
                        <tr><td class="text-muted">{{ __('client.show_status') }}</td><td>
                            @php
                                $jColor = match($booking->serviceJob->status) {
                                    'pending' => 'secondary',
                                    'accepted' => 'primary',
                                    'checked_in' => 'warning',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $jColor }}">{{ __('client.job_status_' . $booking->serviceJob->status) }}</span>
                        </td></tr>
                        <tr><td class="text-muted">{{ __('client.show_job_date') }}</td><td>{{ $booking->serviceJob->job_date->format('d M Y') }}</td></tr>
                        @if($booking->serviceJob->assignee)
                            <tr><td class="text-muted">{{ __('client.show_therapist') }}</td><td>{{ $booking->serviceJob->assignee->name }}</td></tr>
                        @endif
                    </table>
                </div>
            @else
                <h6 class="fw-bold mb-3"><i class="fas fa-briefcase me-2" style="color:var(--warm-accent)"></i>{{ __('client.show_assigned_job') }}</h6>
                <p class="text-muted small">{{ __('client.show_no_job') }}</p>
            @endif
        </div>
    </div>

    {{-- Review Section --}}
    @if($booking->serviceJob && $booking->serviceJob->status === 'completed')
        <div class="mt-4 pt-4" style="border-top:1px solid var(--warm-border);">
            @php $existingReview = $booking->serviceJob->review; @endphp
            @if($existingReview)
                <h6 class="fw-bold mb-3"><i class="fas fa-star me-2" style="color:var(--warm-accent)"></i>{{ __('client.review_your_review') }}</h6>
                <div style="border:1px solid var(--warm-border);border-radius:8px;padding:1rem;">
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $existingReview->rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        @php
                            $rColor = match($existingReview->status) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $rColor }} ms-2">{{ __('client.review_' . $existingReview->status) }}</span>
                    </div>
                    @if($existingReview->comment)
                        <p class="small mb-0">{{ $existingReview->comment }}</p>
                    @endif
                </div>
            @else
                <a href="{{ route('client.reviews.create', $booking->serviceJob) }}" class="btn btn-warm btn-sm">
                    <i class="fas fa-star me-1"></i>{{ __('client.review_write') }}
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
