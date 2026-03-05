@extends('layouts.public')

@section('title', 'Confinement & Wellness — Home')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #f8f0e8 0%, #faf6f2 50%, #f3ebe3 100%);
        padding: 5rem 0;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        border-radius: 50%;
        background: rgba(200,149,108,0.08);
    }
    .hero-title {
        font-size: 2.8rem;
        font-weight: 700;
        color: var(--warm-text);
        line-height: 1.2;
    }
    .hero-subtitle {
        font-size: 1.15rem;
        color: var(--warm-muted);
        line-height: 1.7;
    }

    .service-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--warm-border);
        padding: 2rem;
        text-align: center;
        transition: all 0.3s;
        height: 100%;
    }
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(200,149,108,0.15);
        border-color: var(--warm-accent);
    }
    .service-icon {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(200,149,108,0.15), rgba(176,125,88,0.1));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        color: var(--warm-accent);
    }
    .service-price {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--warm-accent);
    }

    .how-it-works { background: #fff; padding: 4rem 0; }
    .step-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--warm-accent), var(--warm-accent-dark));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 auto 1rem;
    }

    .about-section { padding: 4rem 0; }
    .testimonial-section { background: #fff; padding: 4rem 0; }
    .testimonial-card {
        background: var(--warm-bg);
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid var(--warm-border);
    }
    .cta-section {
        background: linear-gradient(135deg, var(--warm-accent), var(--warm-accent-dark));
        padding: 4rem 0;
        color: #fff;
        text-align: center;
    }

    .section-title {
        font-weight: 700;
        color: var(--warm-text);
        margin-bottom: 0.5rem;
    }
    .section-subtitle {
        color: var(--warm-muted);
        margin-bottom: 2.5rem;
    }
</style>
@endpush

@section('content')

    <!-- Hero -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="hero-title mb-3">{!! __('client.hero_title') !!}</h1>
                    <p class="hero-subtitle mb-4">{{ __('client.hero_subtitle') }}</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('public.booking.create') }}" class="btn btn-warm btn-lg">
                            <i class="fas fa-calendar-plus me-2"></i>{{ __('client.hero_book_now') }}
                        </a>
                        <a href="#services" class="btn btn-warm-outline btn-lg">
                            <i class="fas fa-spa me-2"></i>{{ __('client.hero_our_services') }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                    <div style="width:300px;height:300px;border-radius:50%;background:linear-gradient(135deg, rgba(200,149,108,0.2), rgba(176,125,88,0.1));display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-spa" style="font-size:6rem;color:var(--warm-accent);opacity:0.6;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">{{ __('client.services_title') }}</h2>
                <p class="section-subtitle">{{ __('client.services_subtitle') }}</p>
            </div>
            <div class="row g-4">
                @forelse($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $service->service_type }}</h5>
                        @if($service->description)
                            <p class="text-muted small mb-3">{{ Str::limit($service->description, 120) }}</p>
                        @endif
                        @if($service->price)
                            <div class="service-price mb-3">RM {{ number_format($service->price, 2) }}</div>
                        @endif
                        <a href="{{ route('public.booking.create', ['service' => $service->service_type]) }}" class="btn btn-warm btn-sm">
                            {{ __('client.services_book_this') }}
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-muted">{{ __('client.services_coming_soon') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">{{ __('client.how_title') }}</h2>
                <p class="section-subtitle">{{ __('client.how_subtitle') }}</p>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="step-circle">1</div>
                    <h5 class="fw-bold">{{ __('client.how_step1_title') }}</h5>
                    <p class="text-muted small">{{ __('client.how_step1_desc') }}</p>
                </div>
                <div class="col-md-4">
                    <div class="step-circle">2</div>
                    <h5 class="fw-bold">{{ __('client.how_step2_title') }}</h5>
                    <p class="text-muted small">{{ __('client.how_step2_desc') }}</p>
                </div>
                <div class="col-md-4">
                    <div class="step-circle">3</div>
                    <h5 class="fw-bold">{{ __('client.how_step3_title') }}</h5>
                    <p class="text-muted small">{{ __('client.how_step3_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About -->
    <section class="about-section" id="about">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h2 class="section-title">{{ __('client.about_title') }}</h2>
                    <p class="text-muted">{{ __('client.about_p1') }}</p>
                    <p class="text-muted">{{ __('client.about_p2') }}</p>
                    <div class="row g-3 mt-2">
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="small">{{ __('client.about_certified') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="small">{{ __('client.about_home_visit') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="small">{{ __('client.about_nationwide') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="small">{{ __('client.about_flexible') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div style="background:linear-gradient(135deg, rgba(200,149,108,0.15), rgba(176,125,88,0.08));border-radius:20px;padding:3rem;">
                        <i class="fas fa-heart" style="font-size:4rem;color:var(--warm-accent);opacity:0.5;"></i>
                        <p class="mt-3 mb-0 text-muted">{{ __('client.about_tagline') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonial-section">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">{{ __('client.testimonials_title') }}</h2>
                <p class="section-subtitle">{{ __('client.testimonials_subtitle') }}</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--warm-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">A</div>
                            <div>
                                <div class="fw-bold small">Aishah M.</div>
                                <div class="text-muted" style="font-size:0.75rem;">Selangor</div>
                            </div>
                        </div>
                        <p class="small text-muted mb-1">{{ __('client.testimonial_1') }}</p>
                        <div class="text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--warm-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">N</div>
                            <div>
                                <div class="fw-bold small">Nurul H.</div>
                                <div class="text-muted" style="font-size:0.75rem;">Johor</div>
                            </div>
                        </div>
                        <p class="small text-muted mb-1">{{ __('client.testimonial_2') }}</p>
                        <div class="text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--warm-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">S</div>
                            <div>
                                <div class="fw-bold small">Siti R.</div>
                                <div class="text-muted" style="font-size:0.75rem;">Penang</div>
                            </div>
                        </div>
                        <p class="small text-muted mb-1">{{ __('client.testimonial_3') }}</p>
                        <div class="text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <h2 class="fw-bold mb-3">{{ __('client.cta_title') }}</h2>
            <p class="mb-4 opacity-75">{{ __('client.cta_subtitle') }}</p>
            <a href="{{ route('public.booking.create') }}" class="btn btn-light btn-lg fw-bold" style="color:var(--warm-accent);">
                <i class="fas fa-calendar-plus me-2"></i>{{ __('client.nav_book_now') }}
            </a>
        </div>
    </section>

@endsection
