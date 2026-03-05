@extends('layouts.app')

@section('title', 'SOP & Contracts')
@section('page-title', 'SOP & Training Materials')

@push('styles')
<style>
    .material-card {
        border: 1px solid #e8e0d8;
        border-radius: 10px;
        padding: 1.25rem;
        background: #fff;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .material-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .file-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
</style>
@endpush

@section('content')
<h5 class="mb-3" style="color:#3d2c1e;">SOP & Training Materials</h5>

@if($materials->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="fas fa-book fa-3x mb-3 opacity-50"></i>
        <p>No materials available yet.</p>
    </div>
@else
<div class="row g-3">
    @foreach($materials as $material)
    @php
        $ext = strtolower(pathinfo($material->file_path, PATHINFO_EXTENSION));
        $iconClass = match($ext) {
            'pdf' => 'fa-file-pdf',
            'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
            'mp4', 'mov', 'avi' => 'fa-file-video',
            'doc', 'docx' => 'fa-file-word',
            default => 'fa-file',
        };
        $iconColor = match($ext) {
            'pdf' => '#dc3545',
            'jpg', 'jpeg', 'png', 'gif' => '#0dcaf0',
            'mp4', 'mov', 'avi' => '#fd7e14',
            'doc', 'docx' => '#0d6efd',
            default => '#6c757d',
        };
        $iconBg = match($ext) {
            'pdf' => '#fce4ec',
            'jpg', 'jpeg', 'png', 'gif' => '#e0f7fa',
            'mp4', 'mov', 'avi' => '#fff3e0',
            'doc', 'docx' => '#e3f2fd',
            default => '#f1f5f9',
        };
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="material-card">
            <div class="d-flex gap-3">
                <div class="file-icon" style="background:{{ $iconBg }}; color:{{ $iconColor }};">
                    <i class="fas {{ $iconClass }}"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1" style="color:#3d2c1e;">{{ $material->title }}</h6>
                    @if($material->description)
                        <p class="text-muted mb-2" style="font-size:0.8rem;">{{ Str::limit($material->description, 80) }}</p>
                    @endif
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">{{ $material->created_at->format('d M Y') }}</small>
                        <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="btn btn-sm" style="background:#f8f0e8; color:#c8956c;">
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-3">{{ $materials->links() }}</div>
@endif
@endsection
