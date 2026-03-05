@extends('layouts.client')

@section('title', __('client.reviews_title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('client.reviews_title') }}</h4>
        <p class="text-muted small mb-0">{{ __('client.reviews_subtitle') }}</p>
    </div>
</div>

@if($reviews->isEmpty())
    <div class="text-center py-5" style="background:#fff;border-radius:12px;border:1px solid var(--warm-border);">
        <i class="fas fa-star fa-3x mb-3" style="color:var(--warm-border);"></i>
        <h6 class="text-muted">{{ __('client.review_no_reviews') }}</h6>
    </div>
@else
    @foreach($reviews as $review)
    <div class="mb-3" style="background:#fff;border-radius:12px;border:1px solid var(--warm-border);padding:1.25rem;">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <h6 class="fw-bold mb-1">{{ $review->serviceJob->service_type ?? '-' }}</h6>
                <div class="small text-muted">{{ $review->serviceJob->job_code ?? '-' }}</div>
            </div>
            @php
                $statusColor = match($review->status) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary',
                };
            @endphp
            <span class="badge bg-{{ $statusColor }}">{{ __('client.review_' . $review->status) }}</span>
        </div>
        <div class="mb-2">
            @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size:1.1rem;"></i>
            @endfor
            <span class="ms-2 small text-muted">{{ $review->user->name ?? '-' }}</span>
        </div>
        @if($review->comment)
            <p class="mb-1 small">{{ $review->comment }}</p>
        @endif
        <div class="small text-muted">{{ $review->created_at->format('d M Y, g:i A') }}</div>
    </div>
    @endforeach

    {{ $reviews->links() }}
@endif
@endsection
