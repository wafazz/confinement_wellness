@extends('layouts.app')

@section('title', 'Create Job')
@section('page-title', 'Create Job')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Create New Job</h5>
        <a href="{{ route('leader.jobs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('leader.jobs.store') }}" method="POST">
            @csrf
            @include('leader.jobs._form')
            <button type="submit" class="btn" style="background:linear-gradient(135deg,#c8956c,#b07d58);color:#fff;">
                <i class="fas fa-save me-1"></i> Create Job
            </button>
        </form>
    </div>
</div>
@endsection
