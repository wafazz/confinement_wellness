@extends('layouts.public')

@section('title', 'Book a Session — Confinement & Wellness')

@push('styles')
<style>
    .booking-form {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--warm-border);
        padding: 2rem;
    }
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--warm-border);
    }
    .form-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .form-section-title {
        font-weight: 600;
        color: var(--warm-text);
        margin-bottom: 1rem;
        font-size: 1.05rem;
    }
    .form-section-title i { color: var(--warm-accent); margin-right: 0.5rem; }

    .service-radio-card {
        border: 2px solid var(--warm-border);
        border-radius: 10px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        height: 100%;
    }
    .service-radio-card:hover { border-color: var(--warm-accent); }
    .service-radio-card.selected {
        border-color: var(--warm-accent);
        background: rgba(200,149,108,0.05);
        box-shadow: 0 0 0 3px rgba(200,149,108,0.15);
    }
    .service-radio-card .form-check-input:checked { background-color: var(--warm-accent); border-color: var(--warm-accent); }

    .booking-sidebar {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--warm-border);
        padding: 1.5rem;
        position: sticky;
        top: 80px;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-1" style="color:var(--warm-text);">{{ __('client.booking_title') }}</h2>
            <p class="text-muted mb-4">{{ __('client.booking_subtitle') }}</p>

            <form action="{{ route('public.booking.store') }}" method="POST" id="bookingForm">
                @csrf

                <!-- Service Selection -->
                <div class="booking-form mb-4">
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-spa"></i>{{ __('client.booking_select_service') }}</div>
                        <div class="row g-3">
                            @foreach($services as $service)
                            <div class="col-md-6">
                                <label class="service-radio-card d-block {{ old('service_type', request('service')) === $service->service_type ? 'selected' : '' }}">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="service_type" value="{{ $service->service_type }}"
                                            {{ old('service_type', request('service')) === $service->service_type ? 'checked' : '' }} required>
                                        <div class="ms-1">
                                            <div class="fw-bold">{{ $service->service_type }}</div>
                                            @if($service->price)
                                                <div class="fw-bold" style="color:var(--warm-accent);">RM {{ number_format($service->price, 2) }}</div>
                                            @endif
                                            @if($service->description)
                                                <div class="text-muted small mt-1">{{ Str::limit($service->description, 80) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('service_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Schedule -->
                <div class="booking-form mb-4">
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-calendar-alt"></i>{{ __('client.booking_schedule') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('client.booking_preferred_date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('preferred_date') is-invalid @enderror"
                                    name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" required>
                                @error('preferred_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('client.booking_preferred_time') }} <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('preferred_time') is-invalid @enderror"
                                    name="preferred_time" value="{{ old('preferred_time') }}" required>
                                @error('preferred_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="booking-form mb-4">
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-map-marker-alt"></i>{{ __('client.booking_location') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('client.booking_state') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('state') is-invalid @enderror" name="state" id="stateSelect" required>
                                    <option value="">{{ __('client.booking_select_state') }}</option>
                                    @php
                                        $allStates = ['Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Pulau Pinang','Sabah','Sarawak','Selangor','Terengganu','W.P. Kuala Lumpur','W.P. Labuan','W.P. Putrajaya'];
                                    @endphp
                                    @foreach($allStates as $s)
                                        <option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('client.booking_district') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror"
                                    name="district" value="{{ old('district') }}" required placeholder="{{ __('client.booking_district_placeholder') }}">
                                @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('client.booking_full_address') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('client_address') is-invalid @enderror"
                                    name="client_address" rows="2" required placeholder="{{ __('client.booking_address_placeholder') }}">{{ old('client_address', Auth::guard('client')->check() ? Auth::guard('client')->user()->address : '') }}</textarea>
                                @error('client_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferred Therapist -->
                <div class="booking-form mb-4">
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-user-nurse"></i>{{ __('client.booking_preferred_therapist') }} <span class="text-muted fw-normal small">{{ __('client.booking_optional') }}</span></div>
                        <select class="form-select" name="preferred_therapist_id" id="therapistSelect">
                            <option value="">{{ __('client.booking_no_preference') }}</option>
                        </select>
                        <div class="form-text">{{ __('client.booking_state_hint') }}</div>
                    </div>
                </div>

                <!-- Client Details -->
                <div class="booking-form mb-4">
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-user"></i>{{ __('client.booking_your_details') }}</div>
                        @if(!Auth::guard('client')->check())
                            <div class="alert alert-info small mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                {!! __('client.booking_login_hint', ['url' => route('client.login')]) !!}
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('client.booking_full_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                    name="client_name" value="{{ old('client_name', Auth::guard('client')->check() ? Auth::guard('client')->user()->name : '') }}" required>
                                @error('client_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('client.booking_phone') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('client_phone') is-invalid @enderror"
                                    name="client_phone" value="{{ old('client_phone', Auth::guard('client')->check() ? Auth::guard('client')->user()->phone : '') }}" required placeholder="{{ __('client.booking_phone_placeholder') }}">
                                @error('client_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('client.booking_email') }}</label>
                                <input type="email" class="form-control @error('client_email') is-invalid @enderror"
                                    name="client_email" value="{{ old('client_email', Auth::guard('client')->check() ? Auth::guard('client')->user()->email : '') }}" placeholder="{{ __('client.booking_email_placeholder') }}">
                                @error('client_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="booking-form mb-4">
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-sticky-note"></i>{{ __('client.booking_notes') }} <span class="text-muted fw-normal small">{{ __('client.booking_optional') }}</span></div>
                        <textarea class="form-control" name="notes" rows="3" placeholder="{{ __('client.booking_notes_placeholder') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Referral Code -->
                <div class="booking-form mb-4">
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-gift"></i>Referral Code <span class="text-muted fw-normal small">(Optional)</span></div>
                        <input type="text" class="form-control @error('referral_code') is-invalid @enderror"
                            name="referral_code" value="{{ old('referral_code', request('ref')) }}" placeholder="e.g. REF-XXXXX or CREF-XXXXX">
                        @error('referral_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Have a referral code? Enter it here to support your referrer.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warm btn-lg w-100">
                    <i class="fas fa-paper-plane me-2"></i>{{ __('client.booking_submit') }}
                </button>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="booking-sidebar">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2" style="color:var(--warm-accent)"></i>{{ __('client.booking_info_title') }}</h6>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('client.booking_info_1') }}</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('client.booking_info_2') }}</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('client.booking_info_3') }}</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('client.booking_info_4') }}</li>
                    <li class="mb-2"><i class="fas fa-clock text-warning me-2"></i>{{ __('client.booking_info_5') }}</li>
                </ul>
                <hr>
                <h6 class="fw-bold mb-2 small">{{ __('client.booking_need_help') }}</h6>
                <p class="small text-muted mb-1"><i class="fas fa-phone me-2"></i>+60 12-345 6789</p>
                <p class="small text-muted mb-0"><i class="fas fa-envelope me-2"></i>info@confinementwellness.com</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Service radio card selection
    $('input[name="service_type"]').on('change', function() {
        $('.service-radio-card').removeClass('selected');
        $(this).closest('.service-radio-card').addClass('selected');
    });

    // Load therapists by state
    $('#stateSelect').on('change', function() {
        var state = $(this).val();
        var $select = $('#therapistSelect');
        $select.html('<option value="">{{ __("client.booking_no_preference") }}</option>');

        if (state) {
            $.get('{{ route("public.booking.therapists") }}', { state: state }, function(data) {
                data.forEach(function(t) {
                    $select.append('<option value="' + t.id + '">' + t.name + '</option>');
                });
            });
        }
    });

    // Trigger on page load if state pre-selected
    if ($('#stateSelect').val()) {
        $('#stateSelect').trigger('change');
    }
});
</script>
@endpush
