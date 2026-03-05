@extends('layouts.app')

@section('title', 'Leader Team')
@section('page-title', $leader->name . "'s Team")

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>{{ $leader->name }}'s Team ({{ $therapists->count() }})</h5>
        <a href="{{ route('hq.leaders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Leaders
        </a>
    </div>
    <div class="card-body">
        @if($therapists->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p>No therapists under this leader yet.</p>
            </div>
        @else
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>District</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($therapists as $t)
                        @php $color = match($t->status) { 'active' => 'success', 'inactive' => 'danger', 'pending' => 'warning' }; @endphp
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->name }}</td>
                            <td>{{ $t->email }}</td>
                            <td>{{ $t->phone }}</td>
                            <td>{{ $t->district }}</td>
                            <td><span class="badge bg-{{ $color }}">{{ ucfirst($t->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
