@extends('layouts.app')

@section('title', 'View Therapist')
@section('page-title', 'Therapist Details')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user me-2"></i>{{ $therapist->name }}</h5>
        <a href="{{ route('leader.therapists.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body">
        @php $statusColor = match($therapist->status) { 'active' => 'success', 'inactive' => 'danger', 'pending' => 'warning' }; @endphp
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
                    <tr><th class="text-muted" style="width:40%">State</th><td>{{ $therapist->state }}</td></tr>
                    <tr><th class="text-muted">District</th><td>{{ $therapist->district }}</td></tr>
                    <tr><th class="text-muted">KKM Cert No.</th><td>{{ $therapist->kkm_cert_no ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Bank</th><td>{{ $therapist->bank_name ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Bank Account</th><td>{{ $therapist->bank_account ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
