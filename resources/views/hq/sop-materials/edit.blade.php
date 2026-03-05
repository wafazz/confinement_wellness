@extends('layouts.app')

@section('title', 'Edit Material')
@section('page-title', 'Edit SOP Material')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('hq.sop-materials.update', $material) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('hq.sop-materials._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update</button>
                <a href="{{ route('hq.sop-materials.index') }}" class="btn btn-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
