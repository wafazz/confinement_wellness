# Confinement & Wellness

**Agent Management System for Post-Natal Care & Wellness Services**

A full-featured web application for managing a network of confinement care therapists across Malaysia. Built with a 3-tier hierarchy (HQ → Leader → Therapist) plus a client booking portal, the system handles the entire lifecycle from booking to service delivery, commission payouts, and performance tracking.

---

## Features

### Staff Management (HQ → Leader → Therapist)
- **HQ Admin** — Full control over leaders, therapists, jobs, commissions, and system settings
- **Leader** — Manage own team of therapists, assign jobs, monitor performance, earn override commissions
- **Therapist** — Accept jobs, GPS check-in/check-out at client locations, track earnings and points
- Role-based dashboards with Chart.js analytics, stat cards, and activity feeds
- Profile management with photo upload

### Job Lifecycle
- Auto-generated job codes (`JOB-YYYYMMDD-NNN`)
- Job status flow: **Pending → Accepted → Checked In → Completed**
- GPS-based check-in/check-out with browser geolocation (mobile-first)
- Live timer during active sessions
- Multi-day job support (Stay-In, Daily Visit categories) with daily records
- Work updates with text + photo during service
- Visual status timeline on all job detail pages
- Leader self-assignment with full lifecycle support

### Commission Engine
- **3 commission types**: Direct (therapist), Override (leader), Affiliate (referral-based)
- **Rate types**: Fixed RM amount or percentage of service price — configurable per service type
- Auto-calculation triggered on check-out
- Commission status flow: **Pending → Approved → Paid** (managed by HQ)
- Monthly summary views for all roles
- PDF statement download (branded templates via DomPDF)
- Bulk approve/pay by month (HQ)

### Points & Gamification
- Points awarded per job completion (configurable per service type)
- Reward tiers (Bronze, Silver, Gold) — managed by HQ
- Monthly and lifetime point tracking
- Leaderboard with 3 filters: Nationwide, State, Team

### Client Booking Portal
- Public landing page with service showcase
- Booking form with service selection, scheduling, location, and optional therapist preference
- Auto-create or review-based booking flow (configurable per service type)
- Client authentication (separate guard)
- Client dashboard with booking tracking, active jobs, and reward points
- Referral system — clients and staff share referral links (`/book?ref=CODE`)

### Referral & Affiliate System
- Staff referral codes (`REF-XXXXX`), client referral codes (`CREF-XXXXX`)
- Affiliate commission on referred bookings (staff)
- Client reward points on referred bookings (client-to-client)
- Referral link with copy button on all dashboards

### Customer Reviews
- Clients rate their therapist/leader (1-5 stars + comment) after completed jobs
- HQ approval workflow (Pending → Approved / Rejected)
- Approved reviews visible on staff job detail pages
- Review management DataTable for HQ

### Notifications
- 5 notification types: Job Assigned, Job Completed, Commission Approved, Commission Paid, Booking Received
- Bell icon with unread count in topbar
- Mark as read / mark all as read

### SOP & Training Materials
- HQ uploads documents (PDF, images)
- Card-based view/download for leaders and therapists

### Progressive Web App (PWA)
- Installable on mobile devices
- Offline fallback page
- Cache-first for static assets, network-first for pages

### Multi-Language (BM/EN)
- Session-based locale toggle (Bahasa Malaysia / English)
- Client-facing pages only (public + client portal)
- 180+ translation keys

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.3 |
| Frontend | Blade + Bootstrap 5 + jQuery |
| Database | MySQL 8 |
| Cache / Queue / Session | Redis |
| Auth | Laravel Breeze (staff) + Custom guard (client) |
| Roles & Permissions | Spatie Laravel-Permission |
| DataTables | Yajra DataTables (server-side) |
| Charts | Chart.js 4 |
| PDF Export | barryvdh/laravel-dompdf |
| Server | Nginx + PHP-FPM 8.3 |
| SSL | Let's Encrypt (Certbot) |

---

## Project Stats

| Metric | Count |
|--------|-------|
| Models | 13 |
| Controllers | 43 |
| Blade Views | 100 |
| Routes | 157 |
| Migrations | 25 |
| Notification Classes | 5 |
| Language Keys | 180+ (EN + BM) |

---

## Architecture

```
┌─────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                  │
│              Bootstrap 5 + jQuery + Chart.js         │
└──────────────────────┬──────────────────────────────┘
                       │ HTTPS
                       ▼
┌─────────────────────────────────────────────────────┐
│              NGINX (Reverse Proxy + SSL)             │
└──────────────────────┬──────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────┐
│              LARAVEL 12 (PHP-FPM 8.3)               │
│                                                     │
│  Breeze Auth │ Spatie Roles │ Yajra DataTables      │
│  DomPDF      │ Notifications │ Commission Engine    │
└───────┬──────────────────────────────┬──────────────┘
        │                              │
        ▼                              ▼
┌───────────────┐              ┌───────────────┐
│   MySQL 8     │              │    Redis      │
│  (Database)   │              │ (Cache/Queue  │
│               │              │  /Session)    │
└───────────────┘              └───────────────┘
```

### User Role Hierarchy

```
                    ┌──────────────┐
                    │   HQ Admin   │
                    │ (Full Control)│
                    └──────┬───────┘
                           │ manages
              ┌────────────┼────────────┐
              ▼                         ▼
     ┌──────────────┐         ┌──────────────┐
     │   Leader A   │         │   Leader B   │
     │ (Selangor)   │         │  (Johor)     │
     └──────┬───────┘         └──────┬───────┘
            │ manages                │ manages
       ┌────┼────┐              ┌────┼────┐
       ▼         ▼              ▼         ▼
  Therapist  Therapist     Therapist  Therapist
      1          2             3          4
```

### Client Portal Flow

```
  Client (separate auth guard)
       │
       ├── Public Landing Page (services, how it works, testimonials)
       ├── Book a Session (form → auto-assign or pending review)
       ├── Client Dashboard (stats, active jobs, bookings)
       ├── My Bookings (track status, view linked job)
       └── My Reviews (rate therapist after completed job)
```

---

## Database Schema

| Table | Purpose |
|-------|---------|
| `users` | Staff accounts (HQ, Leader, Therapist) with referral codes |
| `clients` | Client accounts (separate auth guard) with referral codes |
| `service_jobs` | Job records with GPS check-in/out, multi-day support |
| `job_daily_records` | Per-day records for multi-day jobs (Stay-In, Daily Visit) |
| `job_updates` | Work updates (text + photo) during active jobs |
| `bookings` | Client booking requests with referral tracking |
| `commissions` | Commission records (direct, override, affiliate) |
| `commission_rules` | Service type configs (rates, points, pricing, review flow) |
| `points` | Points awarded per job |
| `reward_tiers` | Tier definitions (Bronze, Silver, Gold) |
| `reviews` | Client reviews with 1-5 star rating (HQ-approved) |
| `client_reward_points` | Client referral reward points audit trail |
| `sop_materials` | Uploaded training documents |
| `notifications` | Laravel notifications (database driver) |

---

## Installation (Local Development)

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 20+
- MySQL 8
- Redis

### Setup

```bash
# Clone repository
git clone <repo-url> confinement-wellness
cd confinement-wellness

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
```

Edit `.env` with your local database credentials:

```env
APP_NAME="Confinement & Wellness"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=confinement_wellness
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

```bash
# Generate app key
php artisan key:generate

# Run migrations & seed sample data
php artisan migrate
php artisan db:seed

# Create storage symlink (for uploads)
php artisan storage:link

# Build frontend assets
npm run build

# Start development server
php artisan serve --port=8000
```

Visit `http://localhost:8000`

### Test Accounts

| Role | Email | Password |
|------|-------|----------|
| HQ Admin | admin@confinement.com | password |
| Staff | staff@confinement.com | password |
| Leader | leader@confinement.com | password |
| Therapist 1 | therapist1@confinement.com | password |
| Therapist 2 | therapist2@confinement.com | password |
| Client | client@confinement.com | password |
| Client | nadia@example.com | password |

---

## Production Deployment

See **[DEPLOY.md](DEPLOY.md)** for full step-by-step deployment instructions:

- Ubuntu 24.04 LTS (DigitalOcean VPS)
- Nginx + PHP-FPM 8.3
- MySQL 8 + Redis
- Let's Encrypt SSL
- Queue worker (systemd)
- Scheduler (cron)
- OPcache tuning
- Quick deploy script

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── HQ/              # HQ admin controllers (11)
│   ├── Leader/          # Leader controllers (6)
│   ├── Therapist/       # Therapist controllers (7)
│   ├── Client/          # Client portal controllers (3)
│   ├── Public/          # Public pages (2)
│   └── Auth/            # Breeze auth controllers
├── Models/              # 13 Eloquent models
└── Notifications/       # 5 notification classes

resources/views/
├── layouts/             # app (staff), client, public, auth
├── hq/                  # HQ admin pages
├── leader/              # Leader pages
├── therapist/           # Therapist pages
├── client/              # Client portal pages
├── public/              # Landing page, booking form
└── notifications/       # Notification pages

lang/
├── en/client.php        # English translations (180+ keys)
└── ms/client.php        # Bahasa Malaysia translations

database/
├── migrations/          # 25 migration files
└── seeders/             # Database seeders with sample data

public/
├── manifest.webmanifest # PWA manifest
├── sw.js                # Service worker
├── offline.html         # Offline fallback page
└── icons/               # PWA app icons (192x192, 512x512)
```

---

## Design Theme

| Element | HQ Admin | Leader / Therapist | Client Portal |
|---------|----------|-------------------|---------------|
| Sidebar | Dark navy (#1e293b) | Warm cream gradient (#faf6f2 → #f3ebe3) | Top navbar (white) |
| Accent | Indigo (#4f46e5) | Warm brown (#c8956c → #b07d58) | Warm brown (#c8956c) |
| Active Nav | Dark hover | Gradient + box-shadow + 8px radius | Accent text color |
| Background | Slate (#f1f5f9) | Warm bg (#faf6f2) | Warm bg (#faf6f2) |

---

## Key Workflows

### Job Lifecycle
```
Leader creates job → Therapist accepts → GPS Check-In → Service delivery → GPS Check-Out
                                                                              │
                                              ┌───────────────────────────────┤
                                              ▼                               ▼
                                    Commission auto-calculated       Points awarded
                                    (direct + override + affiliate)
                                              │
                                              ▼
                                    HQ approves → marks as paid
```

### Booking Flow
```
Client submits booking
        │
        ├── requires_review = false → Auto-creates job + assigns therapist
        │
        └── requires_review = true  → Pending review
                                           │
                                    HQ/Leader approves
                                           │
                                    HQ/Leader converts to job
```

### Review Flow
```
Job completed → Client writes review (1-5 stars)
                        │
                  Status: Pending
                        │
                  HQ approves/rejects
                        │
                  Approved → visible on staff job pages
```

---

## Notes

- All dates in Malaysian timezone (`Asia/Kuala_Lumpur`)
- Currency in MYR (RM)
- Mobile-responsive design (therapists use mobile phones in the field)
- GPS check-in/out via browser Geolocation API

---

## License

Proprietary. All rights reserved.
