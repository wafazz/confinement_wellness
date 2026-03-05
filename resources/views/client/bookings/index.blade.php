@extends('layouts.client')

@section('title', 'My Bookings — Client Portal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">{{ __('client.bookings_title') }}</h4>
        <p class="text-muted mb-0 small">{{ __('client.bookings_subtitle') }}</p>
    </div>
    <a href="{{ route('public.booking.create') }}" class="btn btn-warm btn-sm">
        <i class="fas fa-plus me-1"></i>{{ __('client.bookings_new') }}
    </a>
</div>

@if($bookings->count())
    @foreach($bookings as $booking)
    <a href="{{ route('client.bookings.show', $booking) }}" class="d-block text-decoration-none mb-3" style="color:inherit;">
        <div style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);padding:1.25rem;transition:all 0.2s;" onmouseover="this.style.borderColor='var(--warm-accent)'" onmouseout="this.style.borderColor='var(--warm-border)'">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold">{{ $booking->booking_code }}</div>
                    <div class="text-muted small">{{ $booking->service_type }}</div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-calendar me-1"></i>{{ $booking->preferred_date->format('d M Y') }}
                        &middot;
                        <i class="fas fa-clock ms-1 me-1"></i>{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }}
                        &middot;
                        <i class="fas fa-map-marker-alt ms-1 me-1"></i>{{ $booking->district }}, {{ $booking->state }}
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $color = match($booking->status) {
                            'pending_review' => 'warning',
                            'approved' => 'info',
                            'converted' => 'success',
                            'rejected' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $color }}">{{ __('client.status_' . $booking->status) }}</span>
                    <div class="text-muted small mt-1">{{ $booking->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
    </a>
    @endforeach

    <div class="d-flex justify-content-center mt-3">
        {{ $bookings->links() }}
    </div>
@else
    <div class="text-center py-5" style="background:#fff;border-radius:10px;border:1px solid var(--warm-border);">
        <i class="fas fa-calendar-times mb-3" style="font-size:3rem;color:var(--warm-border);"></i>
        <h5 class="text-muted">{{ __('client.bookings_empty_title') }}</h5>
        <p class="text-muted small">{{ __('client.bookings_empty_desc') }}</p>
        <a href="{{ route('public.booking.create') }}" class="btn btn-warm btn-sm">{{ __('client.nav_book_now') }}</a>
    </div>
@endif
@endsection
