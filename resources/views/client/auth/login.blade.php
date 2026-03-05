@extends('layouts.public')

@section('title', 'Client Login — Confinement & Wellness')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold" style="color:var(--warm-text);">{{ __('client.login_title') }}</h3>
                <p class="text-muted">{{ __('client.login_subtitle') }}</p>
            </div>
            <div style="background:#fff;border-radius:12px;border:1px solid var(--warm-border);padding:2rem;">
                <form method="POST" action="{{ route('client.login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('client.login_email') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autofocus>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('client.login_password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">{{ __('client.login_remember') }}</label>
                    </div>
                    <button type="submit" class="btn btn-warm w-100 mb-3">
                        <i class="fas fa-sign-in-alt me-1"></i>{{ __('client.login_submit') }}
                    </button>
                    <div class="text-center small">
                        {{ __('client.login_no_account') }} <a href="{{ route('client.register') }}" style="color:var(--warm-accent);">{{ __('client.login_register_link') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
