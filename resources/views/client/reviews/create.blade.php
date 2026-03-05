@extends('layouts.client')

@section('title', __('client.review_write'))

@push('styles')
<style>
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 0.25rem; }
    .star-rating input { display: none; }
    .star-rating label { cursor: pointer; font-size: 2rem; color: #e8ddd3; transition: color 0.15s; }
    .star-rating label:hover, .star-rating label:hover ~ label,
    .star-rating input:checked ~ label { color: #f0ad4e; }
</style>
@endpush

@section('content')
<a href="{{ route('client.bookings.index') }}" class="text-decoration-none small" style="color:var(--warm-accent);">
    <i class="fas fa-arrow-left me-1"></i>{{ __('client.show_back') }}
</a>

<div class="mt-3" style="background:#fff;border-radius:12px;border:1px solid var(--warm-border);padding:2rem;max-width:600px;">
    <h5 class="fw-bold mb-1">{{ __('client.review_write') }}</h5>
    <p class="text-muted small mb-3">{{ $job->service_type }} — {{ $job->job_code }}</p>

    @if($job->assignee)
        <div class="d-flex align-items-center mb-4 p-3" style="background:var(--warm-bg);border-radius:8px;">
            <div style="width:45px;height:45px;border-radius:50%;background:linear-gradient(135deg,#c8956c,#a0735a);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;">
                {{ strtoupper(substr($job->assignee->name, 0, 1)) }}
            </div>
            <div class="ms-3">
                <div class="fw-bold">{{ $job->assignee->name }}</div>
                <div class="small text-muted">{{ ucfirst($job->assignee->role) }}</div>
            </div>
        </div>
    @endif

    <form action="{{ route('client.reviews.store', $job) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="form-label fw-bold">{{ __('client.review_rating') }} <span class="text-danger">*</span></label>
            <div class="star-rating">
                @for($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                    <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                @endfor
            </div>
            @error('rating') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">{{ __('client.review_comment') }}</label>
            <textarea name="comment" class="form-control" rows="4" maxlength="1000" placeholder="{{ __('client.review_comment_placeholder') }}">{{ old('comment') }}</textarea>
            @error('comment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-warm">
            <i class="fas fa-paper-plane me-1"></i>{{ __('client.review_submit') }}
        </button>
    </form>
</div>
@endsection
