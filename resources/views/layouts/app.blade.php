<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

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
            --sidebar-width: 260px;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --topbar-height: 60px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
        }

        /* ── Default (HQ) dark sidebar ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: #fff;
            z-index: 1040;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            white-space: nowrap;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.6rem 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
            border-radius: 0;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar-section {
            padding: 0.75rem 1.25rem 0.25rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
        }

        /* ── Warm clinic sidebar (Leader & Therapist) ── */
        .sidebar.sidebar-warm {
            background: linear-gradient(180deg, #faf6f2 0%, #f3ebe3 100%);
            color: #3d2c1e;
            border-right: 1px solid #e8ddd3;
        }

        .sidebar-warm .sidebar-brand {
            border-bottom: 1px solid #e8ddd3;
            color: #3d2c1e;
        }

        .sidebar-warm .sidebar-profile {
            padding: 1.5rem 1.25rem;
            text-align: center;
            border-bottom: 1px solid #e8ddd3;
        }

        .sidebar-warm .sidebar-profile .profile-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c8956c, #a0735a);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0 auto 0.6rem;
            border: 3px solid #e8ddd3;
        }

        .sidebar-warm .sidebar-profile .profile-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #3d2c1e;
            margin-bottom: 0.15rem;
        }

        .sidebar-warm .sidebar-profile .profile-role {
            font-size: 0.75rem;
            color: #8b6f5e;
            margin-bottom: 0.15rem;
        }

        .sidebar-warm .sidebar-profile .profile-login {
            font-size: 0.7rem;
            color: #b09a8a;
        }

        .sidebar-warm .sidebar-nav .nav-link {
            color: #6b5648;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            margin: 2px 0.6rem;
        }

        .sidebar-warm .sidebar-nav .nav-link:hover {
            background: rgba(200,149,108,0.15);
            color: #3d2c1e;
        }

        .sidebar-warm .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, #c8956c, #b07d58);
            color: #fff;
            box-shadow: 0 2px 8px rgba(200,149,108,0.3);
        }

        .sidebar-warm .sidebar-nav .nav-link.active i {
            color: #fff;
        }

        .sidebar-warm .sidebar-section {
            color: #b09a8a;
        }

        .sidebar-warm .sidebar-nav .nav-link.logout-link {
            color: #c0392b;
        }
        .sidebar-warm .sidebar-nav .nav-link.logout-link:hover {
            background: rgba(192,57,43,0.1);
            color: #a93226;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1030;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 1.5rem;
            min-height: calc(100vh - var(--topbar-height));
        }

        .sidebar-toggle { display: none; }

        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1035;
            }
            .sidebar-overlay.show { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar @if(!in_array(Auth::user()->role, ['hq', 'staff'])) sidebar-warm @endif" id="sidebar">
        @if(in_array(Auth::user()->role, ['hq', 'staff']))
            {{-- HQ / Staff: dark sidebar with brand --}}
            <div class="sidebar-brand">
                <i class="fas fa-spa me-2"></i> C&W System
            </div>
            <nav class="sidebar-nav mt-2">
                <div class="sidebar-section">Main</div>
                <a href="{{ route('hq.dashboard') }}" class="nav-link {{ request()->routeIs('hq.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <div class="sidebar-section">Management</div>
                @can('access-leaders')
                <a href="{{ route('hq.leaders.index') }}" class="nav-link {{ request()->routeIs('hq.leaders.*') ? 'active' : '' }}"><i class="fas fa-user-tie"></i> Leaders</a>
                @endcan
                @can('access-therapists')
                <a href="{{ route('hq.therapists.index') }}" class="nav-link {{ request()->routeIs('hq.therapists.*') ? 'active' : '' }}"><i class="fas fa-users"></i> Therapists</a>
                @endcan
                @can('access-staff')
                <a href="{{ route('hq.staff.index') }}" class="nav-link {{ request()->routeIs('hq.staff.*') ? 'active' : '' }}"><i class="fas fa-user-shield"></i> Staff</a>
                @endcan
                @can('access-jobs')
                <a href="{{ route('hq.jobs.index') }}" class="nav-link {{ request()->routeIs('hq.jobs.*') ? 'active' : '' }}"><i class="fas fa-briefcase"></i> Jobs</a>
                @endcan
                @can('access-bookings')
                <a href="{{ route('hq.bookings.index') }}" class="nav-link {{ request()->routeIs('hq.bookings.*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Bookings</a>
                @endcan
                <div class="sidebar-section">Finance</div>
                @can('access-commissions')
                <a href="{{ route('hq.commissions.index') }}" class="nav-link {{ request()->routeIs('hq.commissions.*') ? 'active' : '' }}"><i class="fas fa-money-bill-wave"></i> Commissions</a>
                @endcan
                @can('access-points')
                <a href="{{ route('hq.points.index') }}" class="nav-link {{ request()->routeIs('hq.points.*') ? 'active' : '' }}"><i class="fas fa-star"></i> Points</a>
                @endcan
                @can('access-commission-rules')
                <a href="{{ route('hq.commission-rules.index') }}" class="nav-link {{ request()->routeIs('hq.commission-rules.*') ? 'active' : '' }}"><i class="fas fa-cog"></i> Commission Rules</a>
                @endcan
                @can('access-reward-tiers')
                <a href="{{ route('hq.reward-tiers.index') }}" class="nav-link {{ request()->routeIs('hq.reward-tiers.*') ? 'active' : '' }}"><i class="fas fa-trophy"></i> Reward Tiers</a>
                @endcan
                @can('access-reports')
                <a href="{{ route('hq.reports.index') }}" class="nav-link {{ request()->routeIs('hq.reports.*') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Reports</a>
                @endcan
                <div class="sidebar-section">Resources</div>
                @can('access-sop-materials')
                <a href="{{ route('hq.sop-materials.index') }}" class="nav-link {{ request()->routeIs('hq.sop-materials.*') ? 'active' : '' }}"><i class="fas fa-book"></i> SOP Materials</a>
                @endcan
                @can('access-reviews')
                <a href="{{ route('hq.reviews.index') }}" class="nav-link {{ request()->routeIs('hq.reviews.*') ? 'active' : '' }}"><i class="fas fa-star"></i> Reviews</a>
                @endcan
                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"><i class="fas fa-bell"></i> Notifications</a>
            </nav>
        @else
            {{-- Leader & Therapist: warm clinic sidebar with profile --}}
            <div class="sidebar-brand">
                <i class="fas fa-spa me-2" style="color:#c8956c;"></i> C&W System
            </div>

            <div class="sidebar-profile">
                @if(Auth::user()->profile_photo)
                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile" style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:3px solid #e8ddd3;margin:0 auto 0.6rem;">
                @else
                    <div class="profile-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="profile-name">{{ Auth::user()->name }}</div>
                <div class="profile-role">
                    @if(Auth::user()->role === 'leader')
                        State Leader Therapist
                    @else
                        Therapist
                    @endif
                    @if(Auth::user()->state)
                        ({{ Auth::user()->state }})
                    @endif
                </div>
                <div class="profile-login">
                    <i class="fas fa-clock me-1"></i> Last login: {{ now()->format('d M Y') }}
                </div>
            </div>

            <nav class="sidebar-nav mt-2">
                @role('leader')
                    <a href="{{ route('leader.dashboard') }}" class="nav-link {{ request()->routeIs('leader.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('leader.jobs.index') }}" class="nav-link {{ request()->routeIs('leader.jobs.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i> Job Assignments
                    </a>
                    <a href="{{ route('leader.bookings.index') }}" class="nav-link {{ request()->routeIs('leader.bookings.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> Bookings
                    </a>
                    <a href="{{ route('leader.therapists.index') }}" class="nav-link {{ request()->routeIs('leader.therapists.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Team Management
                    </a>
                    <a href="{{ route('leader.commissions.index') }}" class="nav-link {{ request()->routeIs('leader.commissions.*') ? 'active' : '' }}">
                        <i class="fas fa-wallet"></i> Commission
                    </a>
                    <a href="{{ route('leader.sop-materials.index') }}" class="nav-link {{ request()->routeIs('leader.sop-materials.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract"></i> SOP & Contracts
                    </a>
                @endrole

                @role('therapist')
                    <a href="{{ route('therapist.dashboard') }}" class="nav-link {{ request()->routeIs('therapist.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('therapist.jobs.index') }}" class="nav-link {{ request()->routeIs('therapist.jobs.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i> My Jobs
                    </a>
                    <a href="{{ route('therapist.commissions.index') }}" class="nav-link {{ request()->routeIs('therapist.commissions.*') ? 'active' : '' }}">
                        <i class="fas fa-wallet"></i> Commission
                    </a>
                    <a href="{{ route('therapist.points.index') }}" class="nav-link {{ request()->routeIs('therapist.points.*') ? 'active' : '' }}">
                        <i class="fas fa-star"></i> Points & Rewards
                    </a>
                    <a href="{{ route('therapist.leaderboard') }}" class="nav-link {{ request()->routeIs('therapist.leaderboard') ? 'active' : '' }}">
                        <i class="fas fa-ranking-star"></i> Leaderboard
                    </a>
                    <a href="{{ route('therapist.sop-materials.index') }}" class="nav-link {{ request()->routeIs('therapist.sop-materials.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract"></i> SOP & Contracts
                    </a>
                @endrole

                <div style="margin-top:auto; padding-top:1rem; border-top:1px solid #e8ddd3; margin:1rem 0.6rem 0;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link logout-link w-100 border-0 bg-transparent text-start" style="cursor:pointer;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>
        @endif
    </aside>

    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h6 class="mb-0 text-muted d-none d-md-block">@yield('page-title', 'Dashboard')</h6>
        </div>
        <div class="d-flex align-items-center gap-3">
            @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light position-relative">
                <i class="fas fa-bell"></i>
                @if($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </a>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
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
    </main>

    <!-- jQuery + Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>
    <script>if('serviceWorker' in navigator){navigator.serviceWorker.register('/sw.js')}</script>
    @stack('scripts')
</body>
</html>
