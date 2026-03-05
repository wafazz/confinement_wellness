@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-user me-2"></i>My Profile</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('therapist.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                    <small class="text-muted">Contact your leader to change email.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">IC Number</label>
                    <input type="text" class="form-control" value="{{ $user->ic_number }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                    <select class="form-select @error('state') is-invalid @enderror" id="state" name="state" required>
                        <option value="">Select State</option>
                        @foreach(['Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Pulau Pinang','Sabah','Sarawak','Selangor','Terengganu','W.P. Kuala Lumpur','W.P. Putrajaya','W.P. Labuan'] as $s)
                            <option value="{{ $s }}" {{ old('state', $user->state) == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="district" class="form-label">District <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('district') is-invalid @enderror" id="district" name="district" value="{{ old('district', $user->district) }}" required>
                    @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="kkm_cert_no" class="form-label">KKM Cert No.</label>
                    <input type="text" class="form-control @error('kkm_cert_no') is-invalid @enderror" id="kkm_cert_no" name="kkm_cert_no" value="{{ old('kkm_cert_no', $user->kkm_cert_no) }}">
                    @error('kkm_cert_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="bank_name" class="form-label">Bank Name</label>
                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}">
                    @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="bank_account" class="form-label">Bank Account No.</label>
                    <input type="text" class="form-control @error('bank_account') is-invalid @enderror" id="bank_account" name="bank_account" value="{{ old('bank_account', $user->bank_account) }}">
                    @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr>
            <h6 class="mb-3">Change Password <small class="text-muted">(leave blank to keep current)</small></h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
