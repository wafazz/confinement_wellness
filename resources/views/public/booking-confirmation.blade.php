@extends('layouts.public')

@section('title', 'Booking Confirmed — Confinement & Wellness')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="text-center" style="background:#fff;border-radius:12px;border:1px solid var(--warm-border);padding:2.5rem;">
                @if($booking->status === 'converted' || $booking->status === 'approved')
                    <div style="width:80px;height:80px;border-radius:50%;background:rgba(25,135,84,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                        <i class="fas fa-check-circle text-success" style="font-size:2.5rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-2" style="color:var(--warm-text);">{{ __('client.confirm_title_confirmed') }}</h3>
                    <p class="text-muted mb-3">{{ __('client.confirm_desc_confirmed') }}</p>
                @else
                    <div style="width:80px;height:80px;border-radius:50%;background:rgba(200,149,108,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                        <i class="fas fa-clock" style="font-size:2.5rem;color:var(--warm-accent);"></i>
                    </div>
                    <h3 class="fw-bold mb-2" style="color:var(--warm-text);">{{ __('client.confirm_title_received') }}</h3>
                    <p class="text-muted mb-3">{{ __('client.confirm_desc_received') }}</p>
                @endif

                <div style="background:var(--warm-bg);border-radius:8px;padding:1.5rem;text-align:left;" class="mb-4">
                    <div class="row g-2">
                        <div class="col-5 text-muted small">{{ __('client.confirm_booking_code') }}</div>
                        <div class="col-7 fw-bold">{{ $booking->booking_code }}</div>
                        <div class="col-5 text-muted small">{{ __('client.confirm_service') }}</div>
                        <div class="col-7">{{ $booking->service_type }}</div>
                        <div class="col-5 text-muted small">{{ __('client.confirm_date') }}</div>
                        <div class="col-7">{{ $booking->preferred_date->format('d M Y') }}</div>
                        <div class="col-5 text-muted small">{{ __('client.confirm_time') }}</div>
                        <div class="col-7">{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }}</div>
                        <div class="col-5 text-muted small">{{ __('client.confirm_location') }}</div>
                        <div class="col-7">{{ $booking->district }}, {{ $booking->state }}</div>
                        <div class="col-5 text-muted small">{{ __('client.confirm_status') }}</div>
                        <div class="col-7">
                            @php
                                $color = match($booking->status) {
                                    'pending_review' => 'warning',
                                    'approved', 'converted' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ __('client.status_' . $booking->status) }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ url('/') }}" class="btn btn-warm-outline btn-sm">
                        <i class="fas fa-home me-1"></i>{{ __('client.confirm_back_home') }}
                    </a>
                    @if(Auth::guard('client')->check())
                        <a href="{{ route('client.bookings.index') }}" class="btn btn-warm btn-sm">
                            <i class="fas fa-list me-1"></i>{{ __('client.confirm_my_bookings') }}
                        </a>
                    @else
                        <a href="{{ route('client.register') }}" class="btn btn-warm btn-sm">
                            <i class="fas fa-user-plus me-1"></i>{{ __('client.confirm_create_account') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
