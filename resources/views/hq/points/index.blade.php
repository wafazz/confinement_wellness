@extends('layouts.app')

@section('title', 'Points')
@section('page-title', 'Points')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Points</h5>
        <div class="d-flex gap-2">
            <input type="month" id="filterMonth" class="form-control form-control-sm" style="width:180px;">
            <button id="btnFilter" class="btn btn-sm btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
            <button id="btnClear" class="btn btn-sm btn-secondary">Clear</button>
        </div>
    </div>
    <div class="card-body">
        <table id="pointsTable" class="table table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Job Code</th>
                    <th>Service</th>
                    <th>Points</th>
                    <th>Month</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
var table = $('#pointsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("hq.points.index") }}',
        data: function(d) {
            d.filter_month = $('#filterMonth').val();
        }
    },
    columns: [
        { data: 'id', name: 'id' },
        { data: 'user_name', name: 'user.name' },
        { data: 'user_role', name: 'user.role' },
        { data: 'job_code', name: 'serviceJob.job_code' },
        { data: 'service_type', name: 'serviceJob.service_type' },
        { data: 'points', name: 'points' },
        { data: 'month', name: 'month' }
    ],
    columnDefs: [{ type: 'num', targets: [0, 5] }],
    order: [[0, 'desc']]
});

$('#btnFilter').on('click', function() { table.draw(); });
$('#btnClear').on('click', function() { $('#filterMonth').val(''); table.draw(); });
</script>
@endpush
