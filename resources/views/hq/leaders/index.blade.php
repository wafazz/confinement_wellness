@extends('layouts.app')

@section('title', 'Leaders Management')
@section('page-title', 'Leaders Management')

@push('styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Leaders</h5>
        <a href="{{ route('hq.leaders.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add Leader
        </a>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="leaders-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>State</th>
                    <th>Referral</th>
                    <th>Team Size</th>
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
    $('#leaders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('hq.leaders.index') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'state', name: 'state' },
            { data: 'referral_code', name: 'referral_code' },
            { data: 'team_size', name: 'team_size', searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ type: 'num', targets: 0 }],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
