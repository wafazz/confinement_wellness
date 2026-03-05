<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="ic_number" class="form-label">IC Number <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('ic_number') is-invalid @enderror" id="ic_number" name="ic_number" value="{{ old('ic_number') }}" required>
        @error('ic_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="leader_id" class="form-label">Assign to Leader <span class="text-danger">*</span></label>
        <select class="form-select @error('leader_id') is-invalid @enderror" id="leader_id" name="leader_id" required>
            <option value="">Select Leader</option>
            @foreach($leaders as $leader)
                <option value="{{ $leader->id }}" {{ old('leader_id') == $leader->id ? 'selected' : '' }}>{{ $leader->name }} ({{ $leader->state }})</option>
            @endforeach
        </select>
        @error('leader_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="state" class="form-label">State <span class="text-danger">*</span></label>
        <select class="form-select @error('state') is-invalid @enderror" id="state" name="state" required>
            <option value="">Select State</option>
            @foreach(['Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Pulau Pinang','Sabah','Sarawak','Selangor','Terengganu','W.P. Kuala Lumpur','W.P. Putrajaya','W.P. Labuan'] as $s)
                <option value="{{ $s }}" {{ old('state') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="district" class="form-label">District <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('district') is-invalid @enderror" id="district" name="district" value="{{ old('district') }}" required>
        @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="kkm_cert_no" class="form-label">KKM Cert No.</label>
        <input type="text" class="form-control @error('kkm_cert_no') is-invalid @enderror" id="kkm_cert_no" name="kkm_cert_no" value="{{ old('kkm_cert_no') }}">
        @error('kkm_cert_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="bank_name" class="form-label">Bank Name</label>
        <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
        @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="bank_account" class="form-label">Bank Account No.</label>
        <input type="text" class="form-control @error('bank_account') is-invalid @enderror" id="bank_account" name="bank_account" value="{{ old('bank_account') }}">
        @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
    </div>
</div>
