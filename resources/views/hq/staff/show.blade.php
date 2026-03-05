@extends('layouts.app')

@section('title', 'View Staff')
@section('page-title', 'Staff Details')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>{{ $staff->name }}</h5>
        <div>
            <a href="{{ route('hq.staff.edit', $staff) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('hq.staff.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        @php $statusColor = match($staff->status) { 'active' => 'success', 'inactive' => 'danger', 'pending' => 'warning', default => 'secondary' }; @endphp
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th class="text-muted" style="width:40%">Name</th><td>{{ $staff->name }}</td></tr>
                    <tr><th class="text-muted">Email</th><td>{{ $staff->email }}</td></tr>
                    <tr><th class="text-muted">Phone</th><td>{{ $staff->phone }}</td></tr>
                    <tr><th class="text-muted">IC Number</th><td>{{ $staff->ic_number }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th class="text-muted" style="width:40%">State</th><td>{{ $staff->state }}</td></tr>
                    <tr><th class="text-muted">District</th><td>{{ $staff->district }}</td></tr>
                    <tr><th class="text-muted">Status</th><td><span class="badge bg-{{ $statusColor }}">{{ ucfirst($staff->status) }}</span></td></tr>
                    <tr><th class="text-muted">Joined</th><td>{{ $staff->created_at->format('d M Y') }}</td></tr>
                </table>
            </div>
        </div>

        <hr>
        <h6><i class="fas fa-key me-2"></i>Assigned Permissions</h6>
        <div class="d-flex flex-wrap gap-2 mt-2">
            @forelse($staff->permissions as $perm)
                <span class="badge bg-info fs-6">
                    <i class="fas fa-check me-1"></i>{{ ucfirst(str_replace('access-', '', $perm->name)) }}
                </span>
            @empty
                <span class="text-muted">No permissions assigned</span>
            @endforelse
        </div>
    </div>
</div>
@endsection
