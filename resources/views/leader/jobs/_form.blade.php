<div class="row">
    <div class="col-md-6 mb-3">
        <label for="client_name" class="form-label">Client Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="client_name" name="client_name" value="{{ old('client_name', $job->client_name ?? '') }}" required>
        @error('client_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="client_phone" class="form-label">Client Phone <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('client_phone') is-invalid @enderror" id="client_phone" name="client_phone" value="{{ old('client_phone', $job->client_phone ?? '') }}" required>
        @error('client_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-12 mb-3">
        <label for="client_address" class="form-label">Client Address <span class="text-danger">*</span></label>
        <textarea class="form-control @error('client_address') is-invalid @enderror" id="client_address" name="client_address" rows="2" required>{{ old('client_address', $job->client_address ?? '') }}</textarea>
        @error('client_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="state" class="form-label">State <span class="text-danger">*</span></label>
        <select class="form-select @error('state') is-invalid @enderror" id="state" name="state" required>
            <option value="">Select State</option>
            @foreach(['Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Pulau Pinang','Sabah','Sarawak','Selangor','Terengganu','W.P. Kuala Lumpur','W.P. Putrajaya','W.P. Labuan'] as $s)
                <option value="{{ $s }}" {{ old('state', $job->state ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="district" class="form-label">District <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('district') is-invalid @enderror" id="district" name="district" value="{{ old('district', $job->district ?? '') }}" required>
        @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
        <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
            <option value="">Select Service</option>
            @foreach($commissionRules as $rule)
                <option value="{{ $rule->service_type }}"
                    data-category="{{ $rule->service_category }}"
                    data-work-days="{{ $rule->work_days }}"
                    {{ old('service_type', $job->service_type ?? '') == $rule->service_type ? 'selected' : '' }}>
                    {{ $rule->service_type }}
                    @if($rule->service_category !== 'wellness')
                        ({{ ucfirst(str_replace('_', ' ', $rule->service_category)) }} — {{ $rule->work_days }} days)
                    @endif
                </option>
            @endforeach
        </select>
        @error('service_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div id="category-info" style="display:none;">
            <span id="category-badge" class="badge fs-6 px-3 py-2"></span>
            <span id="work-days-info" class="ms-2 text-muted small"></span>
        </div>
    </div>

    {{-- Single date (Wellness) --}}
    <div class="col-md-3 mb-3 date-field" id="single-date-group">
        <label for="job_date" class="form-label">Job Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('job_date') is-invalid @enderror" id="job_date" name="job_date" value="{{ old('job_date', isset($job) && $job->job_date ? $job->job_date->format('Y-m-d') : '') }}">
        @error('job_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Date range (Stay In / Daily Visit) --}}
    <div class="col-md-3 mb-3 date-field" id="start-date-group" style="display:none;">
        <label for="job_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('job_date') is-invalid @enderror" id="job_start_date" name="job_date" value="{{ old('job_date', isset($job) && $job->job_date ? $job->job_date->format('Y-m-d') : '') }}" disabled>
        @error('job_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3 mb-3 date-field" id="end-date-group" style="display:none;">
        <label for="job_end_date" class="form-label">End Date <small class="text-muted">(auto)</small></label>
        <input type="date" class="form-control" id="job_end_date" name="job_end_date" value="{{ old('job_end_date', isset($job) && $job->job_end_date ? $job->job_end_date->format('Y-m-d') : '') }}" readonly style="background:#f0f0f0;">
    </div>

    <div class="col-md-3 mb-3">
        <label for="job_time" class="form-label">Job Time <span class="text-danger">*</span></label>
        <input type="time" class="form-control @error('job_time') is-invalid @enderror" id="job_time" name="job_time" value="{{ old('job_time', $job->job_time ?? '') }}" required>
        @error('job_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="assigned_to" class="form-label">Assign To <span class="text-danger">*</span></label>
        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to" required>
            <option value="">Select Assignee</option>
            <option value="{{ auth()->id() }}" {{ old('assigned_to', $job->assigned_to ?? '') == auth()->id() ? 'selected' : '' }}>{{ auth()->user()->name }} (Myself)</option>
            @foreach($therapists as $therapist)
                <option value="{{ $therapist->id }}" {{ old('assigned_to', $job->assigned_to ?? '') == $therapist->id ? 'selected' : '' }}>{{ $therapist->name }} ({{ $therapist->state }})</option>
            @endforeach
        </select>
        @error('assigned_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-12 mb-3">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $job->notes ?? '') }}</textarea>
        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var serviceSelect = document.getElementById('service_type');
    var singleDateGroup = document.getElementById('single-date-group');
    var startDateGroup = document.getElementById('start-date-group');
    var endDateGroup = document.getElementById('end-date-group');
    var singleDate = document.getElementById('job_date');
    var startDate = document.getElementById('job_start_date');
    var endDate = document.getElementById('job_end_date');
    var categoryInfo = document.getElementById('category-info');
    var categoryBadge = document.getElementById('category-badge');
    var workDaysInfo = document.getElementById('work-days-info');

    function updateDateFields() {
        var selected = serviceSelect.options[serviceSelect.selectedIndex];
        if (!selected || !selected.value) {
            categoryInfo.style.display = 'none';
            showSingleDate();
            return;
        }

        var category = selected.dataset.category;
        var workDays = parseInt(selected.dataset.workDays) || 0;

        categoryInfo.style.display = '';
        var badgeMap = { stay_in: ['Stay In', 'bg-warning text-dark'], daily_visit: ['Daily Visit', 'bg-info'], wellness: ['Wellness', 'bg-primary'] };
        var info = badgeMap[category] || ['Unknown', 'bg-secondary'];
        categoryBadge.textContent = info[0];
        categoryBadge.className = 'badge fs-6 px-3 py-2 ' + info[1];
        workDaysInfo.textContent = workDays > 0 ? workDays + ' work days' : '';

        if (category === 'stay_in' || category === 'daily_visit') {
            showDateRange(workDays);
        } else {
            showSingleDate();
        }
    }

    function showSingleDate() {
        singleDateGroup.style.display = '';
        startDateGroup.style.display = 'none';
        endDateGroup.style.display = 'none';
        singleDate.disabled = false;
        singleDate.required = true;
        singleDate.name = 'job_date';
        startDate.disabled = true;
        startDate.name = '';
    }

    function showDateRange(workDays) {
        singleDateGroup.style.display = 'none';
        startDateGroup.style.display = '';
        endDateGroup.style.display = '';
        singleDate.disabled = true;
        singleDate.name = '';
        startDate.disabled = false;
        startDate.required = true;
        startDate.name = 'job_date';
        if (singleDate.value && !startDate.value) startDate.value = singleDate.value;
        if (startDate.value && !singleDate.value) singleDate.value = startDate.value;
        calcEndDate(workDays);
    }

    function calcEndDate(workDays) {
        if (startDate.value && workDays > 0) {
            var start = new Date(startDate.value);
            start.setDate(start.getDate() + workDays - 1);
            endDate.value = start.toISOString().split('T')[0];
        } else {
            endDate.value = '';
        }
    }

    serviceSelect.addEventListener('change', updateDateFields);
    startDate.addEventListener('change', function() {
        var selected = serviceSelect.options[serviceSelect.selectedIndex];
        var workDays = parseInt(selected.dataset.workDays) || 0;
        calcEndDate(workDays);
    });

    updateDateFields();
});
</script>
