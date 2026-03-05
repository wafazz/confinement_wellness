@extends('layouts.app')

@section('title', 'Commission Rules')
@section('page-title', 'Commission Rules')

@push('styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Commission Rules</h5>
        <a href="{{ route('hq.commission-rules.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add Rule
        </a>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="rules-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Service Type</th>
                    <th>Days</th>
                    <th>Price</th>
                    <th>Therapist</th>
                    <th>Leader</th>
                    <th>Affiliate</th>
                    <th>Points</th>
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
    $('#rules-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('hq.commission-rules.index') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'category_badge', name: 'service_category', searchable: false },
            { data: 'service_type', name: 'service_type' },
            { data: 'work_days_fmt', name: 'work_days', searchable: false },
            { data: 'price_fmt', name: 'price', searchable: false },
            { data: 'therapist_amt', name: 'therapist_commission' },
            { data: 'leader_amt', name: 'leader_override' },
            { data: 'affiliate_amt', name: 'affiliate_commission', searchable: false },
            { data: 'points_per_job', name: 'points_per_job' },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ type: 'num', targets: 0 }],
        order: [[0, 'asc']]
    });
});
</script>
@endpush
