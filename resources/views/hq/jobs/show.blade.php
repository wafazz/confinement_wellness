@extends('layouts.app')

@section('title', 'Job Details')
@section('page-title', 'Job Details')

@push('styles')
<style>
    .timeline { position: relative; padding-left: 30px; }
    .timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
    .timeline-item { position: relative; margin-bottom: 1.5rem; }
    .timeline-dot { position: absolute; left: -26px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; }
    .timeline-dot.active { background: #198754; box-shadow: 0 0 0 3px rgba(25,135,84,0.2); }
    .timeline-dot.pending { background: #adb5bd; }
    .timeline-dot.current { background: #fd7e14; box-shadow: 0 0 0 3px rgba(253,126,20,0.2); }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>{{ $job->job_code }}</h5>
        <div>
            <a href="{{ route('hq.jobs.edit', $job) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i> Edit</a>
            <a href="{{ route('hq.jobs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        @php
            $statusColor = match($job->status) { 'pending' => 'secondary', 'accepted' => 'primary', 'checked_in' => 'warning', 'completed' => 'success', 'cancelled' => 'danger' };
            $isMultiDay = $job->isMultiDay();
        @endphp

        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Client Information</h6>
                <table class="table table-borderless">
                    <tr><th class="text-muted" style="width:40%">Client Name</th><td>{{ $job->client_name }}</td></tr>
                    <tr><th class="text-muted">Phone</th><td>{{ $job->client_phone }}</td></tr>
                    <tr><th class="text-muted">Address</th><td>{{ $job->client_address }}</td></tr>
                    <tr><th class="text-muted">State</th><td>{{ $job->state }}</td></tr>
                    <tr><th class="text-muted">District</th><td>{{ $job->district }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Job Information</h6>
                <table class="table table-borderless">
                    <tr><th class="text-muted" style="width:40%">Service Type</th><td>{{ $job->service_type }}</td></tr>
                    @if($isMultiDay)
                        <tr><th class="text-muted">Category</th><td><span class="badge bg-{{ $job->service_category === 'stay_in' ? 'warning' : 'info' }}">{{ $job->service_category === 'stay_in' ? 'Stay In' : 'Daily Visit' }}</span></td></tr>
                        <tr><th class="text-muted">Date Range</th><td>{{ $job->job_date->format('d M Y') }} — {{ $job->job_end_date->format('d M Y') }}</td></tr>
                        <tr><th class="text-muted">Work Days</th><td>{{ $job->work_days }} days</td></tr>
                    @else
                        <tr><th class="text-muted">Date</th><td>{{ $job->job_date->format('d M Y') }}</td></tr>
                    @endif
                    <tr><th class="text-muted">Time</th><td>{{ $job->job_time }}</td></tr>
                    <tr><th class="text-muted">Status</th><td><span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span></td></tr>
                    <tr><th class="text-muted">Assigned By</th><td>{{ $job->assigner->name ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Assigned To</th><td>{{ $job->assignee->name ?? '-' }}</td></tr>
                </table>
            </div>
        </div>

        @if($job->notes)
        <div class="mt-2">
            <h6 class="text-muted">Notes</h6>
            <p class="bg-light rounded p-3">{{ $job->notes }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Multi-Day Daily Records (read-only for HQ) --}}
@if($isMultiDay && $job->dailyRecords->count())
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0">
            <i class="fas fa-calendar-alt me-2"></i>Daily Records
            <small class="text-muted ms-2">{{ $job->dailyRecords->where('status', 'completed')->count() }} / {{ $job->work_days }} days completed</small>
        </h6>
    </div>
    <div class="card-body">
        <table class="table table-sm">
            <thead>
                <tr><th>Day</th><th>Date</th><th>Status</th><th>Therapist In</th><th>Therapist Out</th><th>Leader In</th><th>Leader Out</th></tr>
            </thead>
            <tbody>
                @foreach($job->dailyRecords as $record)
                <tr>
                    <td>{{ $record->day_number }}</td>
                    <td>{{ $record->date->format('d M Y') }}</td>
                    <td>
                        @if($record->status === 'completed')
                            <span class="badge bg-success">Done</span>
                        @elseif($record->status === 'checked_in')
                            <span class="badge bg-warning">In Progress</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </td>
                    <td>{{ $record->therapist_check_in_at ? $record->therapist_check_in_at->format('h:i A') : '-' }}</td>
                    <td>{{ $record->therapist_check_out_at ? $record->therapist_check_out_at->format('h:i A') : '-' }}</td>
                    <td>{{ $record->leader_check_in_at ? $record->leader_check_in_at->format('h:i A') : '-' }}</td>
                    <td>{{ $record->leader_check_out_at ? $record->leader_check_out_at->format('h:i A') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Job Timeline --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-stream me-2"></i>Job Timeline</h6>
    </div>
    <div class="card-body">
        <div class="timeline">
            @php
                $steps = ['pending', 'accepted', 'checked_in', 'completed'];
                $currentIndex = array_search($job->status, $steps);
                if ($job->status === 'cancelled') $currentIndex = -1;
            @endphp

            <div class="timeline-item">
                <div class="timeline-dot {{ $currentIndex >= 0 ? 'active' : 'pending' }}"></div>
                <strong>Job Created</strong>
                <div class="text-muted small">{{ $job->created_at->format('d M Y, h:i A') }}</div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot {{ $currentIndex >= 1 ? 'active' : ($currentIndex == 0 ? 'current' : 'pending') }}"></div>
                <strong>Accepted</strong>
                @if($currentIndex >= 1)
                    <div class="text-muted small">Job accepted by {{ $job->assignee->name ?? '-' }}</div>
                @else
                    <div class="text-muted small">Waiting for therapist to accept</div>
                @endif
            </div>

            <div class="timeline-item">
                <div class="timeline-dot {{ $currentIndex >= 2 ? 'active' : ($currentIndex == 1 ? 'current' : 'pending') }}"></div>
                <strong>Checked In</strong>
                @if($job->checked_in_at)
                    <div class="text-muted small">{{ $job->checked_in_at->format('d M Y, h:i A') }}</div>
                    @if($job->checked_in_lat && $job->checked_in_lng)
                        <div class="small mt-1">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            <a href="https://maps.google.com/?q={{ $job->checked_in_lat }},{{ $job->checked_in_lng }}" target="_blank" class="text-decoration-none">
                                {{ $job->checked_in_lat }}, {{ $job->checked_in_lng }}
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-muted small">Pending check-in</div>
                @endif
            </div>

            <div class="timeline-item">
                <div class="timeline-dot {{ $currentIndex >= 3 ? 'active' : ($currentIndex == 2 ? 'current' : 'pending') }}"></div>
                <strong>Completed (Checked Out)</strong>
                @if($job->checked_out_at)
                    <div class="text-muted small">{{ $job->checked_out_at->format('d M Y, h:i A') }}</div>
                    @if($job->checked_out_lat && $job->checked_out_lng)
                        <div class="small mt-1">
                            <i class="fas fa-map-marker-alt text-success me-1"></i>
                            <a href="https://maps.google.com/?q={{ $job->checked_out_lat }},{{ $job->checked_out_lng }}" target="_blank" class="text-decoration-none">
                                {{ $job->checked_out_lat }}, {{ $job->checked_out_lng }}
                            </a>
                        </div>
                    @endif
                    @if($job->checked_in_at && $job->checked_out_at)
                        @php
                            $duration = $job->checked_in_at->diff($job->checked_out_at);
                        @endphp
                        <div class="small mt-1">
                            <i class="fas fa-clock text-primary me-1"></i>
                            Duration: {{ $duration->h }}h {{ $duration->i }}m
                        </div>
                    @endif
                @else
                    <div class="text-muted small">Pending completion</div>
                @endif
            </div>

            @if($job->status === 'cancelled')
            <div class="timeline-item">
                <div class="timeline-dot" style="background:#dc3545;"></div>
                <strong class="text-danger">Cancelled</strong>
                <div class="text-muted small">{{ $job->updated_at->format('d M Y, h:i A') }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Work Updates --}}
@if($job->updates->count())
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Work Updates <span class="badge bg-secondary ms-1">{{ $job->updates->count() }}</span></h6>
    </div>
    <div class="card-body p-0">
        @foreach($job->updates as $update)
        <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <p class="mb-1">{{ $update->description }}</p>
            @if($update->image)
                <a href="{{ asset('storage/' . $update->image) }}" target="_blank">
                    <img src="{{ asset('storage/' . $update->image) }}" alt="Update photo" class="rounded mt-1" style="max-width:200px;max-height:150px;object-fit:cover;border:1px solid #dee2e6;">
                </a>
            @endif
            <div class="text-muted small mt-1">
                <i class="fas fa-clock me-1"></i>{{ $update->created_at->format('d M Y, h:i A') }}
                <span class="ms-2"><i class="fas fa-user me-1"></i>{{ $update->user->name }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Commission & Points --}}
@if($job->commissions->count() || $job->points->count())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-coins me-2"></i>Commission & Points</h6>
    </div>
    <div class="card-body">
        @if($job->commissions->count())
        <h6 class="small text-muted">Commissions</h6>
        <table class="table table-sm mb-3">
            <thead><tr><th>User</th><th>Type</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($job->commissions as $commission)
                <tr>
                    <td>{{ $commission->user->name ?? '-' }}</td>
                    <td><span class="badge bg-{{ $commission->type === 'direct' ? 'primary' : 'info' }}">{{ ucfirst($commission->type) }}</span></td>
                    <td>RM {{ number_format($commission->amount, 2) }}</td>
                    <td><span class="badge bg-{{ $commission->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($commission->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($job->points->count())
        <h6 class="small text-muted">Points Awarded</h6>
        <table class="table table-sm">
            <thead><tr><th>User</th><th>Points</th></tr></thead>
            <tbody>
                @foreach($job->points as $point)
                <tr>
                    <td>{{ $point->user->name ?? '-' }}</td>
                    <td><i class="fas fa-star text-warning me-1"></i>{{ $point->points }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endif

{{-- Customer Review --}}
@if($job->review)
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-star me-2 text-warning"></i>Customer Review</h6>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= $job->review->rating ? 'text-warning' : 'text-muted' }}" style="font-size:1.1rem;"></i>
                @endfor
                <span class="ms-2 small text-muted">by {{ $job->review->client->name ?? '-' }}</span>
            </div>
            @php
                $rColor = match($job->review->status) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary',
                };
            @endphp
            <span class="badge bg-{{ $rColor }}">{{ ucfirst($job->review->status) }}</span>
        </div>
        @if($job->review->comment)
            <p class="mb-1">{{ $job->review->comment }}</p>
        @endif
        <div class="small text-muted">{{ $job->review->created_at->format('d M Y, g:i A') }}</div>
    </div>
</div>
@endif
@endsection
