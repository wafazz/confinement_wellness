@extends('layouts.app')

@section('title', 'Reviews')
@section('page-title', 'Reviews')

@push('styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Customer Reviews</h5>
        <div>
            <select id="filter-status" class="form-select form-select-sm" style="width:auto;display:inline-block;">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="reviews-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Staff</th>
                    <th>Role</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Date</th>
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
    var table = $('#reviews-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('hq.reviews.index') }}',
            data: function(d) {
                d.filter_status = $('#filter-status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'client_name', name: 'client_name', orderable: false },
            { data: 'service_type', name: 'service_type', orderable: false },
            { data: 'staff_name', name: 'staff_name', orderable: false },
            { data: 'staff_role', name: 'staff_role', orderable: false },
            { data: 'stars', name: 'rating', searchable: false },
            { data: 'comment_preview', name: 'comment', searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ type: 'num', targets: 0 }],
        order: [[0, 'desc']]
    });

    $('#filter-status').on('change', function() { table.draw(); });
});
</script>
@endpush
