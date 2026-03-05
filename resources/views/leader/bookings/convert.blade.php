@extends('layouts.app')

@section('title', 'Convert Booking to Job')
@section('page-title', 'Convert Booking')

@section('content')
<a href="{{ route('leader.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="fas fa-arrow-left me-1"></i>Back to Booking
</a>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Convert {{ $booking->booking_code }} to Job</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('leader.bookings.convert', $booking) }}" method="POST">
                    @csrf

                    <div class="alert alert-info small">
                        <strong>Client:</strong> {{ $booking->client_name }} ({{ $booking->client_phone }})<br>
                        <strong>Service:</strong> {{ $booking->service_type }}<br>
                        <strong>Location:</strong> {{ $booking->district }}, {{ $booking->state }}
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Assign to Therapist <span class="text-danger">*</span></label>
                            <select class="form-select @error('assigned_to') is-invalid @enderror" name="assigned_to" required>
                                <option value="">Select Therapist</option>
                                @foreach($therapists as $therapist)
                                    <option value="{{ $therapist->id }}" {{ old('assigned_to', $booking->preferred_therapist_id) == $therapist->id ? 'selected' : '' }}>
                                        {{ $therapist->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Job Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('job_date') is-invalid @enderror"
                                name="job_date" value="{{ old('job_date', $booking->preferred_date->format('Y-m-d')) }}" required>
                            @error('job_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Job Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('job_time') is-invalid @enderror"
                                name="job_time" value="{{ old('job_time', $booking->preferred_time) }}" required>
                            @error('job_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4" onclick="return confirm('Create service job from this booking?')">
                        <i class="fas fa-check me-1"></i>Create Job
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
