<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $tier->title ?? '') }}" placeholder="e.g. Bronze, Silver, Gold">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Minimum Points <span class="text-danger">*</span></label>
        <input type="number" name="min_points" class="form-control @error('min_points') is-invalid @enderror"
            value="{{ old('min_points', $tier->min_points ?? '') }}" min="0">
        @error('min_points') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12">
        <label class="form-label">Reward Description <span class="text-danger">*</span></label>
        <textarea name="reward_description" rows="3" class="form-control @error('reward_description') is-invalid @enderror"
            placeholder="Describe the reward for this tier">{{ old('reward_description', $tier->reward_description ?? '') }}</textarea>
        @error('reward_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" {{ old('status', $tier->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $tier->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
</div>
