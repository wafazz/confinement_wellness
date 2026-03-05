@extends('layouts.app')

@section('title', 'Staff Management')
@section('page-title', 'Staff Management')

@push('styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>All Staff</h5>
        <a href="{{ route('hq.staff.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus me-1"></i> Add Staff
        </a>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="staff-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Permissions</th>
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
    $('#staff-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('hq.staff.index') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'permissions_list', name: 'permissions_list', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ type: 'num', targets: 0 }],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
