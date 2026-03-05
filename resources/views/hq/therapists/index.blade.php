@extends('layouts.app')

@section('title', 'Therapists Management')
@section('page-title', 'Therapists Management')

@push('styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>All Therapists</h5>
        <a href="{{ route('hq.therapists.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus me-1"></i> Add Therapist
        </a>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="therapists-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Leader</th>
                    <th>State</th>
                    <th>Referral</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
    $('#therapists-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('hq.therapists.index') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'leader_name', name: 'leader_name', orderable: false },
            { data: 'state', name: 'state' },
            { data: 'referral_code', name: 'referral_code' },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ type: 'num', targets: 0 }],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
