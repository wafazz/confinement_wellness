@extends('layouts.app')

@section('title', 'Edit Commission Rule')
@section('page-title', 'Edit Commission Rule')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Commission Rule — {{ $rule->service_type }}</h5>
        <a href="{{ route('hq.commission-rules.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('hq.commission-rules.update', $rule) }}" method="POST">
            @csrf
            @method('PUT')
            @include('hq.commission-rules._form')
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Update Rule
            </button>
        </form>
    </div>
</div>
@endsection
