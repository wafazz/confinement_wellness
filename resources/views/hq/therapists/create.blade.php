@extends('layouts.app')

@section('title', 'Add Therapist')
@section('page-title', 'Add New Therapist')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Therapist</h5>
        <a href="{{ route('hq.therapists.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('hq.therapists.store') }}" method="POST">
            @csrf
            @include('hq.therapists._form')
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Create Therapist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
