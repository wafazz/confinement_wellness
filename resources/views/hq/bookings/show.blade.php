@extends('layouts.app')

@section('title', 'Booking ' . $booking->booking_code)
@section('page-title', 'Booking Details')

@section('content')
<a href="{{ route('hq.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="fas fa-arrow-left me-1"></i>Back to Bookings
</a>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $booking->booking_code }}</h5>
                @php
                    $color = match($booking->status) {
                        'pending_review' => 'warning',
                        'approved' => 'info',
                        'converted' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $color }} fs-6">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted small">CLIENT</h6>
                        <p class="mb-1"><strong>{{ $booking->client_name }}</strong></p>
                        <p class="mb-1 small"><i class="fas fa-phone me-1"></i>{{ $booking->client_phone }}</p>
                        @if($booking->client_email)
                            <p class="mb-1 small"><i class="fas fa-envelope me-1"></i>{{ $booking->client_email }}</p>
                        @endif
                        <p class="mb-0 small"><i class="fas fa-map-marker-alt me-1"></i>{{ $booking->client_address }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted small">BOOKING</h6>
                        <p class="mb-1"><strong>{{ $booking->service_type }}</strong></p>
                        <p class="mb-1 small"><i class="fas fa-calendar me-1"></i>{{ $booking->preferred_date->format('d M Y') }}</p>
                        <p class="mb-1 small"><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }}</p>
                        <p class="mb-0 small"><i class="fas fa-map me-1"></i>{{ $booking->district }}, {{ $booking->state }}</p>
                    </div>
                    @if($booking->preferredTherapist)
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted small">PREFERRED THERAPIST</h6>
                        <p class="mb-0">{{ $booking->preferredTherapist->name }}</p>
                    </div>
                    @endif
                    @if($booking->notes)
                    <div class="col-12">
                        <h6 class="fw-bold text-muted small">NOTES</h6>
                        <p class="mb-0">{{ $booking->notes }}</p>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted small">META</h6>
                        <p class="mb-1 small">Source: <span class="badge bg-{{ $booking->source === 'registered' ? 'primary' : 'secondary' }}">{{ ucfirst($booking->source) }}</span></p>
                        <p class="mb-0 small">Submitted: {{ $booking->created_at->format('d M Y, g:i A') }}</p>
                    </div>
                    @if($booking->reviewer)
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted small">REVIEWED BY</h6>
                        <p class="mb-1 small">{{ $booking->reviewer->name }}</p>
                        <p class="mb-0 small">{{ $booking->reviewed_at->format('d M Y, g:i A') }}</p>
                    </div>
                    @endif
                    @if($booking->admin_notes)
                    <div class="col-12">
                        <h6 class="fw-bold text-muted small">ADMIN NOTES</h6>
                        <p class="mb-0">{{ $booking->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($booking->serviceJob)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-briefcase me-2"></i>Linked Job</h6>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $booking->serviceJob->job_code }}</strong></p>
                @php
                    $jColor = match($booking->serviceJob->status) {
                        'pending' => 'secondary', 'accepted' => 'primary', 'checked_in' => 'warning',
                        'completed' => 'success', 'cancelled' => 'danger', default => 'secondary',
                    };
                @endphp
                <p class="mb-1">Status: <span class="badge bg-{{ $jColor }}">{{ ucfirst(str_replace('_', ' ', $booking->serviceJob->status)) }}</span></p>
                @if($booking->serviceJob->assignee)
                    <p class="mb-1 small">Therapist: {{ $booking->serviceJob->assignee->name }}</p>
                @endif
                <a href="{{ route('hq.jobs.show', $booking->serviceJob->id) }}" class="btn btn-sm btn-outline-primary mt-1">View Job</a>
            </div>
        </div>
        @endif
    </div>

    <!-- Actions Sidebar -->
    <div class="col-lg-4">
        @if($booking->status === 'pending_review')
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0">Actions</h6></div>
            <div class="card-body">
                <form action="{{ route('hq.bookings.approve', $booking) }}" method="POST" class="mb-3">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this booking?')">
                        <i class="fas fa-check me-1"></i>Approve Booking
                    </button>
                </form>
                <form action="{{ route('hq.bookings.reject', $booking) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-2">
                        <textarea class="form-control form-control-sm" name="admin_notes" rows="2" placeholder="Reason for rejection..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Reject this booking?')">
                        <i class="fas fa-times me-1"></i>Reject Booking
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($booking->status === 'approved')
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0">Convert to Job</h6></div>
            <div class="card-body">
                <p class="small text-muted">This booking is approved. Convert it to a service job.</p>
                <a href="{{ route('hq.bookings.convert-form', $booking) }}" class="btn btn-primary w-100">
                    <i class="fas fa-exchange-alt me-1"></i>Convert to Job
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
