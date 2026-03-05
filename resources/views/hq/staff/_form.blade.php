<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $staff->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $staff->email ?? '') }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $staff->phone ?? '') }}" required>
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="ic_number" class="form-label">IC Number <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('ic_number') is-invalid @enderror" id="ic_number" name="ic_number" value="{{ old('ic_number', $staff->ic_number ?? '') }}" required>
        @error('ic_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="state" class="form-label">State <span class="text-danger">*</span></label>
        <select class="form-select @error('state') is-invalid @enderror" id="state" name="state" required>
            <option value="">Select State</option>
            @foreach(['Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Pulau Pinang','Sabah','Sarawak','Selangor','Terengganu','W.P. Kuala Lumpur','W.P. Putrajaya','W.P. Labuan'] as $s)
                <option value="{{ $s }}" {{ old('state', $staff->state ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="district" class="form-label">District <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('district') is-invalid @enderror" id="district" name="district" value="{{ old('district', $staff->district ?? '') }}" required>
        @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Password {{ isset($staff) && $staff->exists ? '(leave blank to keep)' : '' }} <span class="text-danger">{{ isset($staff) && $staff->exists ? '' : '*' }}</span></label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" {{ isset($staff) && $staff->exists ? '' : 'required' }}>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
    </div>
</div>

<hr class="my-4">
<h6 class="mb-3"><i class="fas fa-key me-2"></i>Module Permissions <span class="text-danger">*</span></h6>
@error('permissions') <div class="alert alert-danger">{{ $message }}</div> @enderror

@php
    $staffPermissions = isset($staff) && $staff->exists ? $staff->permissions->pluck('name')->toArray() : [];
    $permLabels = [
        'access-leaders' => ['Leaders', 'fa-user-tie'],
        'access-therapists' => ['Therapists', 'fa-users'],
        'access-jobs' => ['Jobs', 'fa-briefcase'],
        'access-bookings' => ['Bookings', 'fa-calendar-check'],
        'access-commissions' => ['Commissions', 'fa-money-bill-wave'],
        'access-points' => ['Points', 'fa-star'],
        'access-commission-rules' => ['Commission Rules', 'fa-cog'],
        'access-reward-tiers' => ['Reward Tiers', 'fa-trophy'],
        'access-sop-materials' => ['SOP Materials', 'fa-book'],
        'access-reviews' => ['Reviews', 'fa-star'],
        'access-staff' => ['Staff Management', 'fa-user-shield'],
    ];
@endphp

<div class="row">
    @foreach($permissions as $perm)
        @php $label = $permLabels[$perm->name] ?? [ucfirst(str_replace('access-', '', $perm->name)), 'fa-lock']; @endphp
        <div class="col-md-4 col-sm-6 mb-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                    {{ in_array($perm->name, old('permissions', $staffPermissions)) ? 'checked' : '' }}>
                <label class="form-check-label" for="perm_{{ $perm->id }}">
                    <i class="fas {{ $label[1] }} me-1 text-muted"></i> {{ $label[0] }}
                </label>
            </div>
        </div>
    @endforeach
</div>
