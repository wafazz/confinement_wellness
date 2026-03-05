<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $material->title ?? '') }}" placeholder="Material title">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
            placeholder="Brief description (optional)">{{ old('description', $material->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-8">
        <label class="form-label">File {{ isset($material) ? '(leave empty to keep current)' : '' }} <span class="text-danger">{{ isset($material) ? '' : '*' }}</span></label>
        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
        <small class="text-muted">Max 20MB. Supports PDF, images, videos, documents.</small>
        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if(isset($material))
            <div class="mt-2">
                <small class="text-muted">Current: {{ basename($material->file_path) }}</small>
            </div>
        @endif
    </div>
</div>
