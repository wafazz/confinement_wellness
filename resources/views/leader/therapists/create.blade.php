@extends('layouts.app')

@section('title', 'Register Therapist')
@section('page-title', 'Register New Therapist')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Register New Therapist</h5>
        <a href="{{ route('leader.therapists.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            New therapists will be set as <strong>Pending</strong> and require HQ approval before they can log in.
        </div>
        <form action="{{ route('leader.therapists.store') }}" method="POST">
            @csrf
            @include('leader.therapists._form')
            <div class="text-end">
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #c8956c, #b07d58); color: #fff;">
                    <i class="fas fa-save me-1"></i> Register Therapist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
