@extends('layouts.app')

@section('title', 'My Jobs')
@section('page-title', 'My Jobs')

@push('styles')
<style>
    .job-card { border-left: 4px solid; transition: transform 0.15s; }
    .job-card:hover { transform: translateY(-2px); }
    .job-card.status-pending { border-color: #6c757d; }
    .job-card.status-accepted { border-color: #0d6efd; }
    .job-card.status-checked_in { border-color: #fd7e14; }
    .job-card.status-completed { border-color: #198754; }
    .job-card.status-cancelled { border-color: #dc3545; }
</style>
@endpush

@section('content')
@if($jobs->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-clipboard-list fa-3x mb-3" style="color:#d5c8bc;"></i>
        <p class="text-muted">No jobs assigned to you yet.</p>
    </div>
@else
    @foreach($jobs as $job)
    @php
        $statusColor = match($job->status) { 'pending' => 'secondary', 'accepted' => 'primary', 'checked_in' => 'warning', 'completed' => 'success', 'cancelled' => 'danger' };
    @endphp
    <div class="card border-0 shadow-sm mb-3 job-card status-{{ $job->status }}">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1" style="color:#3d2c1e;">{{ $job->job_code }}</h6>
                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                    @if($job->isMultiDay())
                        <span class="badge bg-{{ $job->service_category === 'stay_in' ? 'warning' : 'info' }}">{{ $job->service_category === 'stay_in' ? 'Stay In' : 'Daily Visit' }}</span>
                        <small class="text-muted ms-1">{{ $job->work_days }}d</small>
                    @endif
                </div>
                <a href="{{ route('therapist.jobs.show', $job) }}" class="btn btn-sm" style="background:linear-gradient(135deg,#c8956c,#b07d58);color:#fff;">
                    <i class="fas fa-eye me-1"></i> View
                </a>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <div class="small text-muted"><i class="fas fa-user me-1"></i> {{ $job->client_name }}</div>
                    <div class="small text-muted"><i class="fas fa-spa me-1"></i> {{ $job->service_type }}</div>
                </div>
                <div class="col-6 text-end">
                    <div class="small text-muted"><i class="fas fa-calendar me-1"></i> {{ $job->job_date->format('d M Y') }}{{ $job->job_end_date ? ' — ' . $job->job_end_date->format('d M Y') : '' }}</div>
                    <div class="small text-muted"><i class="fas fa-clock me-1"></i> {{ $job->job_time }}</div>
                </div>
            </div>
            <div class="small text-muted mt-1">
                <i class="fas fa-map-marker-alt me-1"></i> {{ $job->client_address }}
            </div>
        </div>
    </div>
    @endforeach

    <div class="d-flex justify-content-center mt-3">
        {{ $jobs->links() }}
    </div>
@endif
@endsection
