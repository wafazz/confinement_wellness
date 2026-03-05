<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Confinement & Wellness')</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#c8956c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --warm-bg: #faf6f2;
            --warm-accent: #c8956c;
            --warm-accent-dark: #b07d58;
            --warm-text: #3d2c1e;
            --warm-muted: #8b6f5e;
            --warm-light: #f8f0e8;
            --warm-border: #e8ddd3;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--warm-bg);
            color: var(--warm-text);
        }

        .navbar-public {
            background: #fff;
            border-bottom: 1px solid var(--warm-border);
            padding: 0.75rem 0;
        }
        .navbar-public .navbar-brand {
            font-weight: 700;
            color: var(--warm-text);
            font-size: 1.2rem;
        }
        .navbar-public .navbar-brand i { color: var(--warm-accent); }
        .navbar-public .nav-link {
            color: var(--warm-muted);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.2s;
        }
        .navbar-public .nav-link:hover,
        .navbar-public .nav-link.active {
            color: var(--warm-accent);
        }

        .btn-warm {
            background: linear-gradient(135deg, var(--warm-accent), var(--warm-accent-dark));
            color: #fff;
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-warm:hover {
            background: linear-gradient(135deg, var(--warm-accent-dark), #96654a);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(200,149,108,0.4);
        }

        .btn-warm-outline {
            border: 2px solid var(--warm-accent);
            color: var(--warm-accent);
            background: transparent;
            font-weight: 600;
            padding: 0.55rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-warm-outline:hover {
            background: var(--warm-accent);
            color: #fff;
        }

        .footer-public {
            background: var(--warm-text);
            color: rgba(255,255,255,0.7);
            padding: 3rem 0 1.5rem;
        }
        .footer-public h6 { color: #fff; font-weight: 600; }
        .footer-public a { color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.2s; }
        .footer-public a:hover { color: var(--warm-accent); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem; margin-top: 2rem; }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-public sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-spa me-2"></i>Confinement & Wellness
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">{{ __('client.nav_home') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#services') }}">{{ __('client.nav_services') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#about') }}">{{ __('client.nav_about') }}</a></li>
                    @if(Auth::guard('client')->check())
                        <li class="nav-item"><a class="nav-link" href="{{ route('client.dashboard') }}">{{ __('client.nav_my_portal') }}</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('client.login') }}">{{ __('client.nav_client_login') }}</a></li>
                    @endif
                    <li class="nav-item ms-lg-2">
                        <div class="btn-group btn-group-sm me-2" role="group">
                            <a href="{{ route('locale.switch', 'en') }}" class="btn {{ app()->getLocale() === 'en' ? 'btn-warm' : 'btn-outline-secondary' }}">EN</a>
                            <a href="{{ route('locale.switch', 'ms') }}" class="btn {{ app()->getLocale() === 'ms' ? 'btn-warm' : 'btn-outline-secondary' }}">BM</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warm btn-sm" href="{{ route('public.booking.create') }}">
                            <i class="fas fa-calendar-plus me-1"></i> {{ __('client.nav_book_now') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    @yield('content')

    <!-- Footer -->
    <footer class="footer-public">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h6><i class="fas fa-spa me-2" style="color:var(--warm-accent)"></i>Confinement & Wellness</h6>
                    <p class="small mt-2 mb-0">{{ __('client.footer_description') }}</p>
                </div>
                <div class="col-lg-3">
                    <h6>{{ __('client.footer_quick_links') }}</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><a href="{{ url('/') }}">{{ __('client.nav_home') }}</a></li>
                        <li class="mb-1"><a href="{{ url('/#services') }}">{{ __('client.nav_services') }}</a></li>
                        <li class="mb-1"><a href="{{ route('public.booking.create') }}">{{ __('client.nav_book_now') }}</a></li>
                        <li class="mb-1"><a href="{{ route('client.login') }}">{{ __('client.nav_client_login') }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6>{{ __('client.footer_contact') }}</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><i class="fas fa-phone me-2"></i>+60 12-345 6789</li>
                        <li class="mb-1"><i class="fas fa-envelope me-2"></i>info@confinementwellness.com</li>
                        <li class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>Kuala Lumpur, Malaysia</li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6>{{ __('client.footer_staff_access') }}</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('login') }}">{{ __('client.footer_staff_login') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <small>{!! __('client.footer_copyright', ['year' => date('Y')]) !!}</small>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>if('serviceWorker' in navigator){navigator.serviceWorker.register('/sw.js')}</script>
    @stack('scripts')
</body>
</html>
