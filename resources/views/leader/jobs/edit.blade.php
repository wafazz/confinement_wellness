@extends('layouts.app')

@section('title', 'Edit Job')
@section('page-title', 'Edit Job')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Job — {{ $job->job_code }}</h5>
        <a href="{{ route('leader.jobs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('leader.jobs.update', $job) }}" method="POST">
            @csrf
            @method('PUT')
            @include('leader.jobs._form')
            <button type="submit" class="btn" style="background:linear-gradient(135deg,#c8956c,#b07d58);color:#fff;">
                <i class="fas fa-save me-1"></i> Update Job
            </button>
        </form>
    </div>
</div>
@endsection
