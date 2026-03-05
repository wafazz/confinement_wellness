@extends('layouts.app')

@section('title', 'Add Leader')
@section('page-title', 'Add New Leader')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Leader</h5>
        <a href="{{ route('hq.leaders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('hq.leaders.store') }}" method="POST">
            @csrf
            @include('hq.leaders._form')
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Create Leader
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
