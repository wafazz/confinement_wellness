@extends('layouts.public')

@section('title', 'Register — Confinement & Wellness')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h3 class="fw-bold" style="color:var(--warm-text);">{{ __('client.register_title') }}</h3>
                <p class="text-muted">{{ __('client.register_subtitle') }}</p>
            </div>
            <div style="background:#fff;border-radius:12px;border:1px solid var(--warm-border);padding:2rem;">
                <form method="POST" action="{{ route('client.register.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('client.register_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('client.register_phone') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                name="phone" value="{{ old('phone') }}" required placeholder="{{ __('client.register_phone_placeholder') }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('client.register_email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('client.register_password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('client.register_confirm_password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        @php
                            $allStates = ['Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Pulau Pinang','Sabah','Sarawak','Selangor','Terengganu','W.P. Kuala Lumpur','W.P. Labuan','W.P. Putrajaya'];
                        @endphp
                        <div class="col-md-6">
                            <label class="form-label">{{ __('client.register_state') }}</label>
                            <select class="form-select" name="state">
                                <option value="">{{ __('client.register_select_state') }}</option>
                                @foreach($allStates as $s)
                                    <option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('client.register_district') }}</label>
                            <input type="text" class="form-control" name="district" value="{{ old('district') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('client.register_address') }}</label>
                            <textarea class="form-control" name="address" rows="2">{{ old('address') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warm w-100 mt-4">
                        <i class="fas fa-user-plus me-1"></i>{{ __('client.register_submit') }}
                    </button>
                    <div class="text-center small mt-3">
                        {{ __('client.register_has_account') }} <a href="{{ route('client.login') }}" style="color:var(--warm-accent);">{{ __('client.register_login_link') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
