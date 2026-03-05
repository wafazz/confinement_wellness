@extends('layouts.app')

@section('title', 'View Therapist')
@section('page-title', 'Therapist Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>{{ $therapist->name }}</h5>
                <a href="{{ route('hq.therapists.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
            <div class="card-body">
                @php
                    $statusColor = match($therapist->status) { 'active' => 'success', 'inactive' => 'danger', 'pending' => 'warning' };
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th class="text-muted" style="width:40%">Name</th><td>{{ $therapist->name }}</td></tr>
                            <tr><th class="text-muted">Email</th><td>{{ $therapist->email }}</td></tr>
                            <tr><th class="text-muted">Phone</th><td>{{ $therapist->phone }}</td></tr>
                            <tr><th class="text-muted">IC Number</th><td>{{ $therapist->ic_number }}</td></tr>
                            <tr><th class="text-muted">Status</th><td><span class="badge bg-{{ $statusColor }}">{{ ucfirst($therapist->status) }}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th class="text-muted" style="width:40%">Leader</th><td>{{ $therapist->leader->name ?? '-' }}</td></tr>
                            <tr><th class="text-muted">State</th><td>{{ $therapist->state }}</td></tr>
                            <tr><th class="text-muted">District</th><td>{{ $therapist->district }}</td></tr>
                            <tr><th class="text-muted">KKM Cert No.</th><td>{{ $therapist->kkm_cert_no ?? '-' }}</td></tr>
                            <tr><th class="text-muted">Bank</th><td>{{ $therapist->bank_name ?? '-' }}</td></tr>
                            <tr><th class="text-muted">Bank Account</th><td>{{ $therapist->bank_account ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Performance</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <small class="text-muted">Total Jobs</small>
                        <h5 class="mb-0">{{ $therapist->total_jobs ?? 0 }}</h5>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Completed</small>
                        <h5 class="mb-0 text-success">{{ $therapist->completed_jobs ?? 0 }}</h5>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <small class="text-muted">Total Commission</small>
                        <h5 class="mb-0">RM {{ number_format($therapist->total_commission ?? 0, 2) }}</h5>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Points</small>
                        <h5 class="mb-0">{{ $therapist->total_points ?? 0 }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
