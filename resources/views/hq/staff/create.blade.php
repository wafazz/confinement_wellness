@extends('layouts.app')

@section('title', 'Add Staff')
@section('page-title', 'Add New Staff')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Staff</h5>
        <a href="{{ route('hq.staff.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('hq.staff.store') }}" method="POST">
            @csrf
            @include('hq.staff._form')
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Create Staff
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
