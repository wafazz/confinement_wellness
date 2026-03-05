@extends('layouts.app')

@section('title', 'Bookings')
@section('page-title', 'Client Bookings')

@push('styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Client Bookings</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="approved">Approved</option>
                    <option value="converted">Converted</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="filterState">
                    <option value="">All States</option>
                    @php
                        $allStates = ['Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Pulau Pinang','Sabah','Sarawak','Selangor','Terengganu','W.P. Kuala Lumpur','W.P. Labuan','W.P. Putrajaya'];
                    @endphp
                    @foreach($allStates as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="filterSource">
                    <option value="">All Sources</option>
                    <option value="guest">Guest</option>
                    <option value="registered">Registered</option>
                </select>
            </div>
        </div>

        <table class="table table-hover" id="bookings-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>State</th>
                    <th>Date</th>
                    <th>Source</th>
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
    var table = $('#bookings-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('hq.bookings.index') }}',
            data: function(d) {
                d.filter_status = $('#filterStatus').val();
                d.filter_state = $('#filterState').val();
                d.filter_source = $('#filterSource').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'booking_code', name: 'booking_code' },
            { data: 'client_name', name: 'client_name' },
            { data: 'service_type', name: 'service_type' },
            { data: 'state', name: 'state' },
            { data: 'preferred_date', name: 'preferred_date' },
            { data: 'source_badge', name: 'source', searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ type: 'num', targets: 0 }],
        order: [[0, 'desc']]
    });

    $('#filterStatus, #filterState, #filterSource').on('change', function() { table.draw(); });
});
</script>
@endpush
