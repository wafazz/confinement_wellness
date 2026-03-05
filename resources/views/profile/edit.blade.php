@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row g-4">
    {{-- Profile Information --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h6>
            </div>
            <div class="card-body">
                @if(session('status') === 'profile-updated')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Profile updated successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    {{-- Profile Photo Upload --}}
                    <div class="mb-4">
                        <label class="form-label">Profile Photo</label>
                        <div class="d-flex align-items-center gap-3">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid #e8ddd3;">
                            @else
                                <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#c8956c,#a0735a);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror" accept="image/*">
                                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">JPG, PNG or GIF. Max 2MB.</small>
                            </div>
                            @if($user->profile_photo)
                                <div>
                                    <label class="form-check-label" style="font-size:0.85rem;">
                                        <input type="checkbox" name="remove_photo" value="1" class="form-check-input"> Remove
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">IC Number</label>
                            <input type="text" class="form-control" value="{{ $user->ic_number }}" disabled>
                            <small class="text-muted">IC number cannot be changed.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $user->state) }}">
                            @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">District</label>
                            <input type="text" name="district" class="form-control @error('district') is-invalid @enderror" value="{{ old('district', $user->district) }}">
                            @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @if($user->role !== 'hq')
                        <div class="col-md-6">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $user->bank_name) }}">
                            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank Account</label>
                            <input type="text" name="bank_account" class="form-control @error('bank_account') is-invalid @enderror" value="{{ old('bank_account', $user->bank_account) }}">
                            @error('bank_account')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif

                        @if($user->kkm_cert_no || $user->role === 'leader')
                        <div class="col-md-6">
                            <label class="form-label">KKM Cert No</label>
                            <input type="text" name="kkm_cert_no" class="form-control @error('kkm_cert_no') is-invalid @enderror" value="{{ old('kkm_cert_no', $user->kkm_cert_no) }}">
                            @error('kkm_cert_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar: Account Info + Change Password --}}
    <div class="col-lg-4">
        {{-- Account Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                @if($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile" class="mx-auto mb-3" style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:3px solid #e8ddd3;">
                @else
                    <div class="mx-auto mb-3" style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#c8956c,#a0735a);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <h6 class="mb-1">{{ $user->name }}</h6>
                <span class="badge bg-{{ $user->role === 'hq' ? 'dark' : ($user->role === 'leader' ? 'primary' : 'success') }}">
                    {{ ucfirst($user->role) }}
                </span>
                @if($user->state)
                    <p class="text-muted mb-0 mt-1" style="font-size:0.85rem;">{{ $user->state }}</p>
                @endif
                <p class="text-muted mb-0" style="font-size:0.8rem;">Member since {{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h6>
            </div>
            <div class="card-body">
                @if(session('status') === 'password-updated')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Password updated successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                        @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                        @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-outline-secondary w-100"><i class="fas fa-key me-1"></i> Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
