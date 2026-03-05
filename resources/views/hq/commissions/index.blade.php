@extends('layouts.app')

@section('title', 'Commissions')
@section('page-title', 'Commission Management')

@push('styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
{{-- Summary Cards --}}
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-warning mb-1"><i class="fas fa-clock fa-2x"></i></div>
                <h4 class="mb-0">RM {{ number_format($totalPending, 2) }}</h4>
                <div class="text-muted small">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-primary mb-1"><i class="fas fa-check-circle fa-2x"></i></div>
                <h4 class="mb-0">RM {{ number_format($totalApproved, 2) }}</h4>
                <div class="text-muted small">Approved</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-success mb-1"><i class="fas fa-money-bill-wave fa-2x"></i></div>
                <h4 class="mb-0">RM {{ number_format($totalPaid, 2) }}</h4>
                <div class="text-muted small">Paid</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters + Bulk Actions --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted">Month</label>
                <select class="form-select form-select-sm" id="filter_month">
                    <option value="">All Months</option>
                    @foreach($months as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::parse($m . '-01')->format('M Y') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select class="form-select form-select-sm" id="filter_status">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Type</label>
                <select class="form-select form-select-sm" id="filter_type">
                    <option value="">All Types</option>
                    <option value="direct">Direct</option>
                    <option value="override">Override</option>
                    <option value="affiliate">Affiliate</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-outline-secondary w-100" id="btn-reset-filter">
                    <i class="fas fa-redo me-1"></i> Reset
                </button>
            </div>
            <div class="col-md-4 text-end">
                <form action="{{ route('hq.commissions.bulk-approve') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="month" id="bulk_approve_month">
                    <button type="submit" class="btn btn-sm btn-primary" onclick="document.getElementById('bulk_approve_month').value=document.getElementById('filter_month').value; return this.form.month.value ? confirm('Approve all pending for this month?') : (alert('Select a month first'),false);">
                        <i class="fas fa-check-double me-1"></i> Bulk Approve
                    </button>
                </form>
                <form action="{{ route('hq.commissions.bulk-paid') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="month" id="bulk_paid_month">
                    <button type="submit" class="btn btn-sm btn-success" onclick="document.getElementById('bulk_paid_month').value=document.getElementById('filter_month').value; return this.form.month.value ? confirm('Mark all approved as paid for this month?') : (alert('Select a month first'),false);">
                        <i class="fas fa-money-bill-wave me-1"></i> Bulk Pay
                    </button>
                </form>
                <a href="#" class="btn btn-sm btn-outline-dark" id="btn-download-pdf" onclick="var m=document.getElementById('filter_month').value; if(!m){alert('Select a month first');return false;} window.location='{{ route('hq.commissions.download-pdf') }}?month='+m; return false;">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
    </div>
</div>

{{-- DataTable --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>All Commissions</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="commissions-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Job Code</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Month</th>
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
    var table = $('#commissions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('hq.commissions.index') }}',
            data: function(d) {
                d.filter_month = $('#filter_month').val();
                d.filter_status = $('#filter_status').val();
                d.filter_type = $('#filter_type').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user_name', name: 'user_name', orderable: false },
            { data: 'user_role', name: 'user_role', orderable: false },
            { data: 'job_code', name: 'job_code', orderable: false },
            { data: 'type_badge', name: 'type', searchable: false },
            { data: 'amount_fmt', name: 'amount' },
            { data: 'month', name: 'month' },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [{ type: 'num', targets: 0 }],
        order: [[0, 'desc']]
    });

    $('#filter_month, #filter_status, #filter_type').on('change', function() { table.draw(); });
    $('#btn-reset-filter').on('click', function() {
        $('#filter_month, #filter_status, #filter_type').val('');
        table.draw();
    });
});
</script>
@endpush
