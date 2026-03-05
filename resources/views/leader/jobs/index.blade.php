@extends('layouts.app')

@section('title', 'Job Assignments')
@section('page-title', 'Job Assignments')

@push('styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Filter by Status</label>
                <select class="form-select form-select-sm" id="filter_status">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="checked_in">Checked In</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-outline-secondary w-100" id="btn-reset-filter">
                    <i class="fas fa-redo me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Team Jobs</h5>
        <a href="{{ route('leader.jobs.create') }}" class="btn btn-sm" style="background:linear-gradient(135deg,#c8956c,#b07d58);color:#fff;">
            <i class="fas fa-plus me-1"></i> Create Job
        </a>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="jobs-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Job Code</th>
                    <th>Client</th>
                    <th>Category</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Therapist</th>
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
    var table = $('#jobs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('leader.jobs.index') }}',
            data: function(d) {
                d.filter_status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'job_code', name: 'job_code' },
            { data: 'client_name', name: 'client_name' },
            { data: 'category_badge', name: 'service_category', searchable: false },
            { data: 'service_type', name: 'service_type' },
            { data: 'job_date', name: 'job_date' },
            { data: 'assigned_to_name', name: 'assigned_to_name', orderable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ type: 'num', targets: 0 }],
        order: [[0, 'desc']]
    });

    $('#filter_status').on('change', function() { table.draw(); });
    $('#btn-reset-filter').on('click', function() { $('#filter_status').val(''); table.draw(); });
});
</script>
@endpush
