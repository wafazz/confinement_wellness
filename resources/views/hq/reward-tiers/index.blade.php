@extends('layouts.app')

@section('title', 'Reward Tiers')
@section('page-title', 'Reward Tiers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Reward Tiers</h5>
    <a href="{{ route('hq.reward-tiers.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Add Tier
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="tiersTable" class="table table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Min Points</th>
                    <th>Reward</th>
                    <th>Status</th>
                    <th>Action</th>
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
$('#tiersTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("hq.reward-tiers.index") }}',
    columns: [
        { data: 'id', name: 'id' },
        { data: 'title', name: 'title' },
        { data: 'min_points', name: 'min_points' },
        { data: 'reward_description', name: 'reward_description' },
        { data: 'status_badge', name: 'status', orderable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    columnDefs: [{ type: 'num', targets: 0 }],
    order: [[2, 'asc']]
});
</script>
@endpush
