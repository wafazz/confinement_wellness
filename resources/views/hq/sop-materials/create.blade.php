@extends('layouts.app')

@section('title', 'Upload Material')
@section('page-title', 'Upload SOP Material')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('hq.sop-materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('hq.sop-materials._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload</button>
                <a href="{{ route('hq.sop-materials.index') }}" class="btn btn-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
