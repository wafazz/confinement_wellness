<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Client Portal — Confinement & Wellness')</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#c8956c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --warm-bg: #faf6f2;
            --warm-accent: #c8956c;
            --warm-accent-dark: #b07d58;
            --warm-text: #3d2c1e;
            --warm-muted: #8b6f5e;
            --warm-border: #e8ddd3;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--warm-bg);
            color: var(--warm-text);
        }

        .client-navbar {
            background: #fff;
            border-bottom: 1px solid var(--warm-border);
            padding: 0.5rem 0;
        }
        .client-navbar .navbar-brand {
            font-weight: 700;
            color: var(--warm-text);
        }
        .client-navbar .navbar-brand i { color: var(--warm-accent); }
        .client-navbar .nav-link {
            color: var(--warm-muted);
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }
        .client-navbar .nav-link:hover,
        .client-navbar .nav-link.active { color: var(--warm-accent); }

        .btn-warm {
            background: linear-gradient(135deg, var(--warm-accent), var(--warm-accent-dark));
            color: #fff;
            border: none;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-warm:hover { background: linear-gradient(135deg, var(--warm-accent-dark), #96654a); color: #fff; }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg client-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('client.dashboard') }}">
                <i class="fas fa-spa me-2"></i>My Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clientNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="clientNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}" href="{{ route('client.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>{{ __('client.nav_dashboard') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.bookings.*') ? 'active' : '' }}" href="{{ route('client.bookings.index') }}">
                            <i class="fas fa-calendar-check me-1"></i>{{ __('client.nav_my_bookings') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.reviews.*') ? 'active' : '' }}" href="{{ route('client.reviews.index') }}">
                            <i class="fas fa-star me-1"></i>{{ __('client.nav_my_reviews') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.booking.create') }}">
                            <i class="fas fa-plus me-1"></i>{{ __('client.nav_book_now') }}
                        </a>
                    </li>
                </ul>
                <div class="btn-group btn-group-sm me-3" role="group">
                    <a href="{{ route('locale.switch', 'en') }}" class="btn {{ app()->getLocale() === 'en' ? 'btn-warm' : 'btn-outline-secondary' }}">EN</a>
                    <a href="{{ route('locale.switch', 'ms') }}" class="btn {{ app()->getLocale() === 'ms' ? 'btn-warm' : 'btn-outline-secondary' }}">BM</a>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>{{ Auth::guard('client')->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('client.logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>{{ __('client.nav_logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
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

        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>if('serviceWorker' in navigator){navigator.serviceWorker.register('/sw.js')}</script>
    @stack('scripts')
</body>
</html>
