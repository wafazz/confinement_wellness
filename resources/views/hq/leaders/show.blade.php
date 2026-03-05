@extends('layouts.app')

@section('title', 'View Leader')
@section('page-title', 'Leader Details')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>{{ $leader->name }}</h5>
        <div>
            <a href="{{ route('hq.leaders.team', $leader) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-users me-1"></i> View Team ({{ $leader->therapists_count }})
            </a>
            <a href="{{ route('hq.leaders.edit', $leader) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('hq.leaders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        @php
            $statusColor = match($leader->status) { 'active' => 'success', 'inactive' => 'danger', 'pending' => 'warning' };
        @endphp
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th class="text-muted" style="width:40%">Name</th><td>{{ $leader->name }}</td></tr>
                    <tr><th class="text-muted">Email</th><td>{{ $leader->email }}</td></tr>
                    <tr><th class="text-muted">Phone</th><td>{{ $leader->phone }}</td></tr>
                    <tr><th class="text-muted">IC Number</th><td>{{ $leader->ic_number }}</td></tr>
                    <tr><th class="text-muted">Status</th><td><span class="badge bg-{{ $statusColor }}">{{ ucfirst($leader->status) }}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th class="text-muted" style="width:40%">State</th><td>{{ $leader->state }}</td></tr>
                    <tr><th class="text-muted">District</th><td>{{ $leader->district }}</td></tr>
                    <tr><th class="text-muted">KKM Cert No.</th><td>{{ $leader->kkm_cert_no ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Bank</th><td>{{ $leader->bank_name ?? '-' }}</td></tr>
                    <tr><th class="text-muted">Bank Account</th><td>{{ $leader->bank_account ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
