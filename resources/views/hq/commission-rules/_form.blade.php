{{-- Service Category Selection --}}
<div class="mb-4">
    <label class="form-label fw-semibold">Service Category <span class="text-danger">*</span></label>
    <div class="row g-3">
        @php
            $currentCategory = old('service_category', $rule->service_category ?? 'wellness');
            $categories = [
                'stay_in' => [
                    'icon' => 'fa-house-user',
                    'label' => 'Stay In',
                    'desc' => 'Therapist stays at customer\'s house. Multi-day with check-in/out.',
                    'color' => '#c8956c',
                ],
                'daily_visit' => [
                    'icon' => 'fa-calendar-check',
                    'label' => 'Daily Visit',
                    'desc' => 'Daily visits to customer. Multi-day with check-in/out per visit.',
                    'color' => '#8b6f5e',
                ],
                'wellness' => [
                    'icon' => 'fa-spa',
                    'label' => 'Wellness',
                    'desc' => 'Single session service. Standard check-in/out flow.',
                    'color' => '#4f46e5',
                ],
            ];
        @endphp
        @foreach ($categories as $value => $cat)
            <div class="col-md-4">
                <label class="d-block cursor-pointer">
                    <input type="radio" name="service_category" value="{{ $value }}" class="d-none category-radio"
                        {{ $currentCategory === $value ? 'checked' : '' }} required>
                    <div class="card border-2 category-card h-100 {{ $currentCategory === $value ? 'selected' : '' }}" data-color="{{ $cat['color'] }}" style="cursor:pointer; transition: all 0.2s;">
                        <div class="card-body text-center py-4">
                            <div class="mb-2">
                                <i class="fas {{ $cat['icon'] }} fa-2x" style="color: {{ $cat['color'] }}"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ $cat['label'] }}</h6>
                            <small class="text-muted">{{ $cat['desc'] }}</small>
                        </div>
                    </div>
                </label>
            </div>
        @endforeach
    </div>
    @error('service_category') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<hr class="mb-4">

{{-- Work Days (Stay In & Daily Visit only) --}}
<div class="row mb-3" id="work-days-row" style="{{ in_array($currentCategory, ['stay_in', 'daily_visit']) ? '' : 'display:none;' }}">
    <div class="col-md-4">
        <label for="work_days" class="form-label">Number of Work Days <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('work_days') is-invalid @enderror" id="work_days" name="work_days"
            value="{{ old('work_days', $rule->work_days ?? '') }}" min="1" max="90" placeholder="e.g. 28">
        @error('work_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">How many days the therapist will work for this service.</small>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('service_type') is-invalid @enderror" id="service_type" name="service_type" value="{{ old('service_type', $rule->service_type ?? '') }}" required placeholder="e.g. Urut Bersalin">
        @error('service_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="price" class="form-label">Client Price (RM)</label>
        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $rule->price ?? '') }}" min="0" placeholder="Price shown to clients">
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12 mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" placeholder="Service description shown on booking page">{{ old('description', $rule->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="points_per_job" class="form-label">Points Per Job <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('points_per_job') is-invalid @enderror" id="points_per_job" name="points_per_job" value="{{ old('points_per_job', $rule->points_per_job ?? '') }}" required min="0">
        @error('points_per_job') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    {{-- Therapist Commission --}}
    <div class="col-md-6 mb-3">
        <label for="therapist_commission" class="form-label">
            Therapist Commission <span class="rate-label" data-target="therapist_commission_type">({{ old('therapist_commission_type', $rule->therapist_commission_type ?? 'fixed') === 'percentage' ? '%' : 'RM' }})</span>
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input type="number" step="0.01" class="form-control @error('therapist_commission') is-invalid @enderror" id="therapist_commission" name="therapist_commission" value="{{ old('therapist_commission', $rule->therapist_commission ?? '') }}" required min="0">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary rate-type-btn {{ old('therapist_commission_type', $rule->therapist_commission_type ?? 'fixed') === 'fixed' ? 'active' : '' }}" data-field="therapist_commission_type" data-value="fixed">RM</button>
                <button type="button" class="btn btn-outline-secondary rate-type-btn {{ old('therapist_commission_type', $rule->therapist_commission_type ?? 'fixed') === 'percentage' ? 'active' : '' }}" data-field="therapist_commission_type" data-value="percentage">%</button>
            </div>
            <input type="hidden" name="therapist_commission_type" id="therapist_commission_type" value="{{ old('therapist_commission_type', $rule->therapist_commission_type ?? 'fixed') }}">
            @error('therapist_commission') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Leader Override --}}
    <div class="col-md-6 mb-3">
        <label for="leader_override" class="form-label">
            Leader Override <span class="rate-label" data-target="leader_override_type">({{ old('leader_override_type', $rule->leader_override_type ?? 'fixed') === 'percentage' ? '%' : 'RM' }})</span>
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input type="number" step="0.01" class="form-control @error('leader_override') is-invalid @enderror" id="leader_override" name="leader_override" value="{{ old('leader_override', $rule->leader_override ?? '') }}" required min="0">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary rate-type-btn {{ old('leader_override_type', $rule->leader_override_type ?? 'fixed') === 'fixed' ? 'active' : '' }}" data-field="leader_override_type" data-value="fixed">RM</button>
                <button type="button" class="btn btn-outline-secondary rate-type-btn {{ old('leader_override_type', $rule->leader_override_type ?? 'fixed') === 'percentage' ? 'active' : '' }}" data-field="leader_override_type" data-value="percentage">%</button>
            </div>
            <input type="hidden" name="leader_override_type" id="leader_override_type" value="{{ old('leader_override_type', $rule->leader_override_type ?? 'fixed') }}">
            @error('leader_override') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Affiliate Commission --}}
    <div class="col-md-6 mb-3">
        <label for="affiliate_commission" class="form-label">
            Affiliate Commission <span class="rate-label" data-target="affiliate_commission_type">({{ old('affiliate_commission_type', $rule->affiliate_commission_type ?? 'fixed') === 'percentage' ? '%' : 'RM' }})</span>
        </label>
        <div class="input-group">
            <input type="number" step="0.01" class="form-control @error('affiliate_commission') is-invalid @enderror" id="affiliate_commission" name="affiliate_commission" value="{{ old('affiliate_commission', $rule->affiliate_commission ?? '0') }}" min="0">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary rate-type-btn {{ old('affiliate_commission_type', $rule->affiliate_commission_type ?? 'fixed') === 'fixed' ? 'active' : '' }}" data-field="affiliate_commission_type" data-value="fixed">RM</button>
                <button type="button" class="btn btn-outline-secondary rate-type-btn {{ old('affiliate_commission_type', $rule->affiliate_commission_type ?? 'fixed') === 'percentage' ? 'active' : '' }}" data-field="affiliate_commission_type" data-value="percentage">%</button>
            </div>
            <input type="hidden" name="affiliate_commission_type" id="affiliate_commission_type" value="{{ old('affiliate_commission_type', $rule->affiliate_commission_type ?? 'fixed') }}">
            @error('affiliate_commission') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <small class="text-muted">Commission for staff/client who referred the booking.</small>
    </div>

    {{-- Customer Referral Points --}}
    <div class="col-md-6 mb-3">
        <label for="customer_referral_points" class="form-label">Customer Referral Points</label>
        <input type="number" class="form-control @error('customer_referral_points') is-invalid @enderror" id="customer_referral_points" name="customer_referral_points" value="{{ old('customer_referral_points', $rule->customer_referral_points ?? '0') }}" min="0">
        @error('customer_referral_points') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">Points awarded to clients who refer others.</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Requires Review</label>
        <div class="form-check form-switch mt-1">
            <input class="form-check-input" type="checkbox" id="requires_review" name="requires_review" value="1"
                {{ old('requires_review', $rule->requires_review ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="requires_review">Bookings require HQ/Leader approval before creating job</label>
        </div>
    </div>
</div>

<style>
    .category-card { border-color: #dee2e6 !important; }
    .category-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .category-card.selected { border-color: var(--sel-color, #c8956c) !important; background: linear-gradient(135deg, rgba(200,149,108,0.05), rgba(200,149,108,0.1)); box-shadow: 0 4px 12px rgba(200,149,108,0.2); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('.category-radio');
    const cards = document.querySelectorAll('.category-card');
    const workDaysRow = document.getElementById('work-days-row');
    const workDaysInput = document.getElementById('work_days');

    function updateSelection() {
        const checked = document.querySelector('.category-radio:checked');
        cards.forEach(c => c.classList.remove('selected'));
        if (checked) {
            const card = checked.closest('label').querySelector('.category-card');
            card.classList.add('selected');
            const color = card.dataset.color;
            card.style.borderColor = color;
            card.style.setProperty('--sel-color', color);

            const val = checked.value;
            if (val === 'stay_in' || val === 'daily_visit') {
                workDaysRow.style.display = '';
                workDaysInput.required = true;
            } else {
                workDaysRow.style.display = 'none';
                workDaysInput.required = false;
                workDaysInput.value = '';
            }
        }
    }

    radios.forEach(r => r.addEventListener('change', updateSelection));
    updateSelection();

    // Rate type toggle buttons
    document.querySelectorAll('.rate-type-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var field = this.dataset.field;
            var value = this.dataset.value;
            document.getElementById(field).value = value;
            // Toggle active class on sibling buttons
            this.parentElement.querySelectorAll('.rate-type-btn').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            // Update label
            var label = document.querySelector('.rate-label[data-target="' + field + '"]');
            if (label) label.textContent = value === 'percentage' ? '(%)' : '(RM)';
        });
    });
});
</script>
