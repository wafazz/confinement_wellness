@extends('layouts.app')

@section('title', 'Job Details')
@section('page-title', 'Job Details')

@push('styles')
<style>
    .timeline { position: relative; padding-left: 30px; }
    .timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: #e8ddd3; }
    .timeline-item { position: relative; margin-bottom: 1.5rem; }
    .timeline-dot { position: absolute; left: -26px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; }
    .timeline-dot.active { background: #c8956c; box-shadow: 0 0 0 3px rgba(200,149,108,0.2); }
    .timeline-dot.pending { background: #d5c8bc; }
    .timeline-dot.current { background: #b07d58; box-shadow: 0 0 0 3px rgba(176,125,88,0.3); animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100% { box-shadow: 0 0 0 3px rgba(176,125,88,0.3); } 50% { box-shadow: 0 0 0 6px rgba(176,125,88,0.1); } }

    .action-btn { padding: 1rem 2rem; font-size: 1.1rem; border-radius: 12px; border: none; color: #fff; width: 100%; }
    .btn-accept { background: linear-gradient(135deg, #0d6efd, #0b5ed7); }
    .btn-checkin { background: linear-gradient(135deg, #fd7e14, #e8690a); }
    .btn-checkout { background: linear-gradient(135deg, #198754, #146c43); }
    .gps-status { padding: 0.75rem; border-radius: 8px; background: #faf6f2; margin-bottom: 1rem; }

    .day-card { border-radius: 12px; border: 1px solid #e8ddd3; transition: all 0.2s; }
    .day-card.today { border-color: #c8956c; box-shadow: 0 2px 8px rgba(200,149,108,0.2); }
    .day-card .day-header { padding: 0.75rem 1rem; border-bottom: 1px solid #f0e8df; background: #faf6f2; border-radius: 12px 12px 0 0; }
    .day-card.completed .day-header { background: #d1e7dd; }
    .day-card.checked_in .day-header { background: #fff3cd; }
</style>
@endpush

@section('content')
@php
    $statusColor = match($job->status) { 'pending' => 'secondary', 'accepted' => 'primary', 'checked_in' => 'warning', 'completed' => 'success', 'cancelled' => 'danger' };
    $isMultiDay = $job->isMultiDay();
@endphp

{{-- Action Buttons (when leader is the assignee — Wellness single-day) --}}
@if($isAssignee && !$isMultiDay && !in_array($job->status, ['completed', 'cancelled']))
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        @if($job->status === 'pending')
            <form action="{{ route('leader.jobs.accept', $job) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="action-btn btn-accept" onclick="return confirm('Accept this job?')">
                    <i class="fas fa-check-circle me-2"></i> Accept Job
                </button>
            </form>
        @elseif($job->status === 'accepted')
            <div class="gps-status" id="gps-status">
                <i class="fas fa-satellite-dish me-2" style="color:#c8956c;"></i>
                <span id="gps-text">Tap "Check In" to capture your GPS location</span>
            </div>
            <form action="{{ route('leader.jobs.wellness-check-in', $job) }}" method="POST" id="checkin-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="latitude" id="checkin-lat">
                <input type="hidden" name="longitude" id="checkin-lng">
                <button type="button" class="action-btn btn-checkin" onclick="captureGPS('checkin')">
                    <i class="fas fa-map-marker-alt me-2"></i> Check In — I'm at Customer Location
                </button>
            </form>
        @elseif($job->status === 'checked_in')
            <div class="gps-status" id="gps-status">
                <i class="fas fa-satellite-dish me-2" style="color:#c8956c;"></i>
                <span id="gps-text">Tap "Check Out" when you're done</span>
            </div>
            <div class="alert alert-info mb-3">
                <i class="fas fa-clock me-2"></i>
                <strong>Checked in at:</strong> {{ $job->checked_in_at->format('h:i A') }}
                <span class="ms-2" id="timer"></span>
            </div>
            <form action="{{ route('leader.jobs.wellness-check-out', $job) }}" method="POST" id="checkout-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="latitude" id="checkout-lat">
                <input type="hidden" name="longitude" id="checkout-lng">
                <button type="button" class="action-btn btn-checkout" onclick="captureGPS('checkout')">
                    <i class="fas fa-sign-out-alt me-2"></i> Check Out — Job Completed
                </button>
            </form>
        @endif
    </div>
</div>
@endif

{{-- Accept button for multi-day (when leader is the assignee) --}}
@if($isAssignee && $isMultiDay && $job->status === 'pending')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form action="{{ route('leader.jobs.accept', $job) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="action-btn btn-accept" onclick="return confirm('Accept this job?')">
                <i class="fas fa-check-circle me-2"></i> Accept Job
            </button>
        </form>
    </div>
</div>
@endif

{{-- Multi-Day Daily Records --}}
@if($isMultiDay && $job->dailyRecords->count() && !in_array($job->status, ['pending', 'cancelled']))
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0">
            <i class="fas fa-calendar-alt me-2" style="color:#c8956c;"></i>Daily Records
            <small class="text-muted ms-2">{{ $job->dailyRecords->where('status', 'completed')->count() }} / {{ $job->work_days }} days completed</small>
        </h6>
    </div>
    <div class="card-body">
        @php $today = now()->format('Y-m-d'); @endphp
        @foreach($job->dailyRecords as $record)
            @php
                $isToday = $record->date->format('Y-m-d') === $today;
                $dayClass = $record->status;
                if ($isToday && $record->status !== 'completed') $dayClass .= ' today';
            @endphp
            <div class="day-card mb-3 {{ $dayClass }}">
                <div class="day-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Day {{ $record->day_number }}</strong>
                        <span class="text-muted ms-2">{{ $record->date->format('d M Y (l)') }}</span>
                        @if($isToday) <span class="badge bg-warning text-dark ms-2">Today</span> @endif
                    </div>
                    <div>
                        @if($record->status === 'completed')
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Completed</span>
                        @elseif($record->status === 'checked_in')
                            <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>In Progress</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </div>
                </div>
                <div class="p-3">
                    <div class="row">
                        {{-- Therapist/Assignee column --}}
                        <div class="col-md-6">
                            <small class="text-muted fw-bold d-block mb-1"><i class="fas fa-user me-1"></i> {{ $isAssignee ? 'You (Assignee)' : 'Therapist' }}</small>
                            @if($record->therapist_check_in_at)
                                <div class="small text-success">
                                    <i class="fas fa-sign-in-alt me-1"></i> In: {{ $record->therapist_check_in_at->format('h:i A') }}
                                    @if($record->therapist_check_in_lat)
                                        <a href="https://maps.google.com/?q={{ $record->therapist_check_in_lat }},{{ $record->therapist_check_in_lng }}" target="_blank" class="text-decoration-none ms-1"><i class="fas fa-map-marker-alt text-danger"></i></a>
                                    @endif
                                </div>
                            @else
                                <div class="small text-muted">Not checked in</div>
                            @endif
                            @if($record->therapist_check_out_at)
                                <div class="small text-info">
                                    <i class="fas fa-sign-out-alt me-1"></i> Out: {{ $record->therapist_check_out_at->format('h:i A') }}
                                    @php $dur = $record->therapist_check_in_at->diff($record->therapist_check_out_at); @endphp
                                    <span class="text-muted ms-1">({{ $dur->h }}h {{ $dur->i }}m)</span>
                                </div>
                            @endif

                            {{-- Assignee check-in/out buttons (leader doing the job) --}}
                            @if($isAssignee && $record->status !== 'completed' && $job->status !== 'cancelled')
                                <div class="mt-2" id="daily-gps-status-{{ $record->id }}" style="display:none;">
                                    <div class="gps-status small">
                                        <span id="daily-gps-text-{{ $record->id }}"></span>
                                    </div>
                                </div>
                                @if(!$record->therapist_check_in_at)
                                    <form action="{{ route('leader.jobs.daily-check-in', $job) }}" method="POST" id="daily-checkin-{{ $record->id }}" class="mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="day_id" value="{{ $record->id }}">
                                        <input type="hidden" name="latitude" id="daily-checkin-lat-{{ $record->id }}">
                                        <input type="hidden" name="longitude" id="daily-checkin-lng-{{ $record->id }}">
                                        <button type="button" class="btn btn-sm px-4 py-2" style="background:linear-gradient(135deg,#fd7e14,#e8690a);color:#fff;border-radius:8px;" onclick="captureDailyGPS('checkin', {{ $record->id }})">
                                            <i class="fas fa-map-marker-alt me-1"></i> Check In Day {{ $record->day_number }}
                                        </button>
                                    </form>
                                @elseif(!$record->therapist_check_out_at)
                                    <div class="alert alert-info py-2 mb-2 small mt-2" id="daily-timer-alert-{{ $record->id }}">
                                        <i class="fas fa-clock me-1"></i> In progress...
                                        <span id="daily-timer-{{ $record->id }}"></span>
                                    </div>
                                    <form action="{{ route('leader.jobs.daily-check-out', $job) }}" method="POST" id="daily-checkout-{{ $record->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="day_id" value="{{ $record->id }}">
                                        <input type="hidden" name="latitude" id="daily-checkout-lat-{{ $record->id }}">
                                        <input type="hidden" name="longitude" id="daily-checkout-lng-{{ $record->id }}">
                                        <button type="button" class="btn btn-sm px-4 py-2" style="background:linear-gradient(135deg,#198754,#146c43);color:#fff;border-radius:8px;" onclick="captureDailyGPS('checkout', {{ $record->id }})">
                                            <i class="fas fa-sign-out-alt me-1"></i> Check Out Day {{ $record->day_number }}
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>

                        {{-- Leader supervision column (only when NOT the assignee) --}}
                        @if(!$isAssignee)
                        <div class="col-md-6">
                            <small class="text-muted fw-bold d-block mb-1"><i class="fas fa-user-tie me-1"></i> Leader (You)</small>
                            @if($record->leader_check_in_at)
                                <div class="small text-success">
                                    <i class="fas fa-sign-in-alt me-1"></i> In: {{ $record->leader_check_in_at->format('h:i A') }}
                                    @if($record->leader_check_in_lat)
                                        <a href="https://maps.google.com/?q={{ $record->leader_check_in_lat }},{{ $record->leader_check_in_lng }}" target="_blank" class="text-decoration-none ms-1"><i class="fas fa-map-marker-alt text-danger"></i></a>
                                    @endif
                                </div>
                            @else
                                <div class="small text-muted">Not checked in</div>
                            @endif
                            @if($record->leader_check_out_at)
                                <div class="small text-info">
                                    <i class="fas fa-sign-out-alt me-1"></i> Out: {{ $record->leader_check_out_at->format('h:i A') }}
                                    @php $dur = $record->leader_check_in_at->diff($record->leader_check_out_at); @endphp
                                    <span class="text-muted ms-1">({{ $dur->h }}h {{ $dur->i }}m)</span>
                                </div>
                            @endif

                            {{-- Leader supervision check-in/out buttons --}}
                            @if($record->status !== 'completed' && $job->status !== 'cancelled')
                                <div class="mt-2" id="leader-gps-{{ $record->id }}" style="display:none;">
                                    <div class="gps-status small">
                                        <span id="leader-gps-text-{{ $record->id }}"></span>
                                    </div>
                                </div>
                                @if(!$record->leader_check_in_at)
                                    <form action="{{ route('leader.jobs.check-in', $job) }}" method="POST" id="leader-checkin-{{ $record->id }}" class="mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="day_id" value="{{ $record->id }}">
                                        <input type="hidden" name="latitude" id="leader-checkin-lat-{{ $record->id }}">
                                        <input type="hidden" name="longitude" id="leader-checkin-lng-{{ $record->id }}">
                                        <button type="button" class="btn btn-sm px-3 py-1" style="background:linear-gradient(135deg,#fd7e14,#e8690a);color:#fff;border-radius:6px;font-size:0.8rem;" onclick="leaderGPS('checkin', {{ $record->id }})">
                                            <i class="fas fa-map-marker-alt me-1"></i> Check In
                                        </button>
                                    </form>
                                @elseif(!$record->leader_check_out_at)
                                    <form action="{{ route('leader.jobs.check-out', $job) }}" method="POST" id="leader-checkout-{{ $record->id }}" class="mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="day_id" value="{{ $record->id }}">
                                        <input type="hidden" name="latitude" id="leader-checkout-lat-{{ $record->id }}">
                                        <input type="hidden" name="longitude" id="leader-checkout-lng-{{ $record->id }}">
                                        <button type="button" class="btn btn-sm px-3 py-1" style="background:linear-gradient(135deg,#198754,#146c43);color:#fff;border-radius:6px;font-size:0.8rem;" onclick="leaderGPS('checkout', {{ $record->id }})">
                                            <i class="fas fa-sign-out-alt me-1"></i> Check Out
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Post Update (visible when leader is assignee and checked in) --}}
@if($isAssignee && $job->status === 'checked_in')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-pen-to-square me-2" style="color:#c8956c;"></i>Post Work Update</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('leader.jobs.post-update', $job) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <textarea class="form-control mb-2 @error('description') is-invalid @enderror" name="description" rows="2" placeholder="What are you working on? e.g. Urut session started, applying herbal wrap..." required>{{ old('description') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <label class="btn btn-sm btn-outline-secondary mb-0" for="update-image">
                        <i class="fas fa-camera me-1"></i> Add Photo
                    </label>
                    <input type="file" id="update-image" name="image" accept="image/*" class="d-none" onchange="document.getElementById('image-name').textContent=this.files[0]?.name||''">
                    <span class="small text-muted ms-2" id="image-name"></span>
                    @error('image') <span class="text-danger small ms-2">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-sm" style="background:linear-gradient(135deg,#c8956c,#b07d58);color:#fff;">
                    <i class="fas fa-paper-plane me-1"></i> Post Update
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>{{ $job->job_code }}</h5>
        <div>
            @if($isMultiDay)
                <span class="badge bg-{{ $job->service_category === 'stay_in' ? 'warning' : 'info' }} me-1">
                    {{ $job->service_category === 'stay_in' ? 'Stay In' : 'Daily Visit' }}
                </span>
            @endif
            <span class="badge bg-{{ $statusColor }} me-1">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
            @if(!in_array($job->status, ['completed', 'cancelled']))
            <a href="{{ route('leader.jobs.edit', $job) }}" class="btn btn-sm" style="background:linear-gradient(135deg,#c8956c,#b07d58);color:#fff;"><i class="fas fa-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('leader.jobs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 style="color:#8b6f5e;" class="mb-3">Client Information</h6>
                <table class="table table-borderless">
                    <tr><th class="text-muted" style="width:40%">Client Name</th><td>{{ $job->client_name }}</td></tr>
                    <tr><th class="text-muted">Phone</th><td><a href="tel:{{ $job->client_phone }}" class="text-decoration-none">{{ $job->client_phone }}</a></td></tr>
                    <tr><th class="text-muted">Address</th><td>{{ $job->client_address }}</td></tr>
                    <tr><th class="text-muted">State</th><td>{{ $job->state }}</td></tr>
                    <tr><th class="text-muted">District</th><td>{{ $job->district }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 style="color:#8b6f5e;" class="mb-3">Job Information</h6>
                <table class="table table-borderless">
                    <tr><th class="text-muted" style="width:40%">Service Type</th><td>{{ $job->service_type }}</td></tr>
                    @if($isMultiDay)
                        <tr><th class="text-muted">Date Range</th><td>{{ $job->job_date->format('d M Y') }} — {{ $job->job_end_date->format('d M Y') }}</td></tr>
                        <tr><th class="text-muted">Work Days</th><td>{{ $job->work_days }} days</td></tr>
                    @else
                        <tr><th class="text-muted">Date</th><td>{{ $job->job_date->format('d M Y') }}</td></tr>
                    @endif
                    <tr><th class="text-muted">Time</th><td>{{ $job->job_time }}</td></tr>
                    <tr><th class="text-muted">Status</th><td><span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span></td></tr>
                    <tr><th class="text-muted">Assigned To</th><td>{{ $job->assignee->name ?? '-' }} @if($isAssignee)<span class="badge bg-info ms-1">You</span>@endif</td></tr>
                </table>
            </div>
        </div>

        @if($job->notes)
        <div class="mt-2">
            <h6 style="color:#8b6f5e;">Notes</h6>
            <p class="rounded p-3" style="background:#faf6f2;">{{ $job->notes }}</p>
        </div>
        @endif

        {{-- Notes form (when leader is assignee) --}}
        @if($isAssignee && !in_array($job->status, ['cancelled']))
        <div class="mt-3">
            <form action="{{ route('leader.jobs.notes', $job) }}" method="POST">
                @csrf
                @method('PATCH')
                <textarea class="form-control mb-2" name="notes" rows="2" placeholder="Add notes or feedback...">{{ old('notes', $job->notes) }}</textarea>
                <button type="submit" class="btn btn-sm" style="background:linear-gradient(135deg,#c8956c,#b07d58);color:#fff;">
                    <i class="fas fa-save me-1"></i> Save Notes
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Job Timeline (Wellness / single-day overview) --}}
@if(!$isMultiDay)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-stream me-2" style="color:#c8956c;"></i>Job Timeline</h6>
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
                    <div class="text-muted small">{{ $isAssignee ? 'Waiting for you to accept' : 'Waiting for assignee to accept' }}</div>
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
                        @php $duration = $job->checked_in_at->diff($job->checked_out_at); @endphp
                        <div class="small mt-1">
                            <i class="fas fa-clock me-1" style="color:#c8956c;"></i>
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
@endif

{{-- Work Updates --}}
@if($job->updates->count())
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-clipboard-list me-2" style="color:#c8956c;"></i>Work Updates <span class="badge bg-secondary ms-1">{{ $job->updates->count() }}</span></h6>
    </div>
    <div class="card-body p-0">
        @foreach($job->updates as $update)
        <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
            <p class="mb-1" style="color:#3d2c1e;">{{ $update->description }}</p>
            @if($update->image)
                <a href="{{ asset('storage/' . $update->image) }}" target="_blank">
                    <img src="{{ asset('storage/' . $update->image) }}" alt="Update photo" class="rounded mt-1" style="max-width:200px;max-height:150px;object-fit:cover;border:1px solid #e8ddd3;">
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
        <h6 class="mb-0"><i class="fas fa-coins me-2" style="color:#c8956c;"></i>Commission & Points</h6>
    </div>
    <div class="card-body">
        @if($job->commissions->count())
        <table class="table table-sm">
            <thead><tr><th>User</th><th>Type</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($job->commissions as $commission)
                <tr>
                    <td>{{ $commission->user->name ?? '-' }}</td>
                    <td><span class="badge" style="background:#c8956c;">{{ ucfirst($commission->type) }}</span></td>
                    <td>RM {{ number_format($commission->amount, 2) }}</td>
                    <td><span class="badge bg-{{ $commission->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($commission->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($job->points->count())
        <h6 class="small text-muted mt-3">Points Awarded</h6>
        @foreach($job->points as $point)
            <span class="me-3"><i class="fas fa-star text-warning me-1"></i>{{ $point->user->name ?? '-' }}: {{ $point->points }} pts</span>
        @endforeach
        @endif
    </div>
</div>
@endif

{{-- Customer Review (approved only) --}}
@if($job->review && $job->review->status === 'approved')
<div class="card border-0 shadow-sm mt-4" style="border-left:4px solid #c8956c !important;">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="fas fa-star text-warning me-2"></i>Customer Review</h6>
        <div class="mb-2">
            @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= $job->review->rating ? 'text-warning' : 'text-muted' }}" style="font-size:1.1rem;"></i>
            @endfor
            <span class="ms-2 small text-muted">by {{ $job->review->client->name ?? '-' }}</span>
        </div>
        @if($job->review->comment)
            <p class="mb-1">{{ $job->review->comment }}</p>
        @endif
        <div class="small text-muted">{{ $job->review->created_at->format('d M Y, g:i A') }}</div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Wellness single-day GPS capture (when leader is assignee)
function captureGPS(type) {
    var gpsText = document.getElementById('gps-text');
    var gpsStatus = document.getElementById('gps-status');

    if (!navigator.geolocation) {
        alert('GPS is not supported on your device.');
        return;
    }

    gpsText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Getting your GPS location...';
    gpsStatus.style.background = '#fff3cd';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            if (type === 'checkin') {
                document.getElementById('checkin-lat').value = lat;
                document.getElementById('checkin-lng').value = lng;
                gpsText.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> GPS captured: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
                gpsStatus.style.background = '#d1e7dd';
                if (confirm('GPS location captured. Confirm check-in?')) {
                    document.getElementById('checkin-form').submit();
                }
            } else {
                document.getElementById('checkout-lat').value = lat;
                document.getElementById('checkout-lng').value = lng;
                gpsText.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> GPS captured: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
                gpsStatus.style.background = '#d1e7dd';
                if (confirm('GPS location captured. Confirm check-out? This will complete the job.')) {
                    document.getElementById('checkout-form').submit();
                }
            }
        },
        function(error) {
            var msg = 'Unable to get GPS location. ';
            switch(error.code) {
                case error.PERMISSION_DENIED: msg += 'Please allow location access.'; break;
                case error.POSITION_UNAVAILABLE: msg += 'Location unavailable.'; break;
                case error.TIMEOUT: msg += 'Request timed out.'; break;
            }
            gpsText.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i> ' + msg;
            gpsStatus.style.background = '#f8d7da';
            alert(msg);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

// Multi-day daily GPS capture (when leader is assignee doing the job)
function captureDailyGPS(type, recordId) {
    var gpsEl = document.getElementById('daily-gps-status-' + recordId);
    var gpsText = document.getElementById('daily-gps-text-' + recordId);

    if (!navigator.geolocation) {
        alert('GPS is not supported on your device.');
        return;
    }

    gpsEl.style.display = '';
    gpsText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Getting your GPS location...';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var prefix = type === 'checkin' ? 'daily-checkin' : 'daily-checkout';

            document.getElementById(prefix + '-lat-' + recordId).value = lat;
            document.getElementById(prefix + '-lng-' + recordId).value = lng;
            gpsText.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> GPS: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);

            var msg = type === 'checkin' ? 'Confirm check-in for this day?' : 'Confirm check-out for this day?';
            if (confirm(msg)) {
                document.getElementById(prefix + '-' + recordId).submit();
            }
        },
        function(error) {
            var msg = 'Unable to get GPS. ';
            switch(error.code) {
                case error.PERMISSION_DENIED: msg += 'Allow location access.'; break;
                case error.POSITION_UNAVAILABLE: msg += 'Location unavailable.'; break;
                case error.TIMEOUT: msg += 'Timed out.'; break;
            }
            gpsText.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i> ' + msg;
            alert(msg);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

// Leader supervision GPS (for multi-day when NOT the assignee)
function leaderGPS(type, recordId) {
    var gpsEl = document.getElementById('leader-gps-' + recordId);
    var gpsText = document.getElementById('leader-gps-text-' + recordId);

    if (!navigator.geolocation) {
        alert('GPS is not supported on your device.');
        return;
    }

    gpsEl.style.display = '';
    gpsText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Getting GPS...';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var prefix = type === 'checkin' ? 'leader-checkin' : 'leader-checkout';

            document.getElementById(prefix + '-lat-' + recordId).value = lat;
            document.getElementById(prefix + '-lng-' + recordId).value = lng;
            gpsText.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> GPS: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);

            var msg = type === 'checkin' ? 'Confirm leader check-in?' : 'Confirm leader check-out?';
            if (confirm(msg)) {
                document.getElementById(prefix + '-' + recordId).submit();
            }
        },
        function(error) {
            var msg = 'Unable to get GPS. ';
            switch(error.code) {
                case error.PERMISSION_DENIED: msg += 'Allow location access.'; break;
                case error.POSITION_UNAVAILABLE: msg += 'Location unavailable.'; break;
                case error.TIMEOUT: msg += 'Timed out.'; break;
            }
            gpsText.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i> ' + msg;
            alert(msg);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

@if($isAssignee && !$isMultiDay && $job->status === 'checked_in' && $job->checked_in_at)
// Wellness live timer
var checkinTime = new Date('{{ $job->checked_in_at->toISOString() }}');
function updateTimer() {
    var now = new Date();
    var diff = Math.floor((now - checkinTime) / 1000);
    var h = Math.floor(diff / 3600);
    var m = Math.floor((diff % 3600) / 60);
    var s = diff % 60;
    document.getElementById('timer').innerHTML = '(' + h + 'h ' + m + 'm ' + s + 's elapsed)';
}
setInterval(updateTimer, 1000);
updateTimer();
@endif

@if($isAssignee && $isMultiDay)
// Multi-day live timers for in-progress records
@foreach($job->dailyRecords as $record)
@if($record->therapist_check_in_at && !$record->therapist_check_out_at)
(function() {
    var t = new Date('{{ $record->therapist_check_in_at->toISOString() }}');
    function tick() {
        var d = Math.floor((new Date() - t) / 1000);
        var el = document.getElementById('daily-timer-{{ $record->id }}');
        if (el) el.innerHTML = Math.floor(d/3600) + 'h ' + Math.floor((d%3600)/60) + 'm ' + (d%60) + 's';
    }
    setInterval(tick, 1000);
    tick();
})();
@endif
@endforeach
@endif
</script>
@endpush
