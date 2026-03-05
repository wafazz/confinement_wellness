@extends('layouts.app')

@section('title', 'SOP Materials')
@section('page-title', 'SOP & Training Materials')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">SOP Materials</h5>
    <a href="{{ route('hq.sop-materials.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-upload me-1"></i> Upload Material
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="sopTable" class="table table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>File</th>
                    <th>Uploaded By</th>
                    <th>Date</th>
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
$('#sopTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("hq.sop-materials.index") }}',
    columns: [
        { data: 'id', name: 'id' },
        { data: 'title', name: 'title' },
        { data: 'description', name: 'description', defaultContent: '-' },
        { data: 'file_link', name: 'file_path', orderable: false },
        { data: 'uploader_name', name: 'uploader.name' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    columnDefs: [{ type: 'num', targets: 0 }],
    order: [[0, 'desc']]
});
</script>
@endpush
