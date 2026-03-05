# Confinement & Women Wellness Agent Management System

## Project Planning — 7-Day Sprint

**Project Name**: Confinement & Wellness Agent Network System
**Tech Stack**: Laravel 12 + PHP 8.3 + MySQL + Bootstrap 5 + Redis
**Server**: VPS Ubuntu 24.04 (4 Core / 8GB RAM / 160GB Disk)
**Start Date**: TBD
**Target Completion**: 7 Days

---

## User Roles

| Role | Access Level |
|------|-------------|
| HQ Admin | Full system control, manage all leaders & therapists |
| Leader Therapist | Manage team, assign jobs, monitor team performance |
| Therapist (Agent) | Personal dashboard, view jobs, track earnings |

---

## Tech Stack & Packages

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.3 |
| Frontend | Blade + Bootstrap 5 + jQuery |
| Database | MySQL |
| Auth | Laravel Breeze |
| Roles & Permissions | Spatie Laravel-Permission |
| DataTables | Yajra DataTables |
| Charts | Chart.js |
| PDF Export | barryvdh/laravel-dompdf |
| Queue & Cache | Redis |
| Notifications | Database + WhatsApp/Telegram (Phase 2) |
| Server | Nginx + PHP-FPM |

---

## Database Schema

### users
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| name | varchar | |
| email | varchar | Unique |
| phone | varchar | |
| ic_number | varchar | MyKad |
| password | varchar | |
| role | enum | hq, leader, therapist |
| leader_id | bigint | FK nullable (therapist's leader) |
| state | varchar | |
| district | varchar | |
| kkm_cert_no | varchar | nullable |
| bank_name | varchar | |
| bank_account | varchar | |
| status | enum | active, inactive, pending |
| profile_photo | varchar | nullable |
| timestamps | | |

### jobs
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| job_code | varchar | Auto-generated unique code |
| client_name | varchar | |
| client_phone | varchar | |
| client_address | text | |
| state | varchar | |
| district | varchar | |
| service_type | varchar | e.g. Urut Bersalin, Bengkung, Tangas |
| job_date | date | |
| job_time | time | |
| assigned_by | bigint | FK (leader) |
| assigned_to | bigint | FK (therapist) |
| status | enum | pending, accepted, checked_in, completed, cancelled |
| notes | text | nullable |
| checked_in_at | datetime | nullable, therapist arrives at customer |
| checked_in_lat | decimal(10,7) | nullable, GPS latitude |
| checked_in_lng | decimal(10,7) | nullable, GPS longitude |
| checked_out_at | datetime | nullable, therapist leaves customer |
| checked_out_lat | decimal(10,7) | nullable, GPS latitude |
| checked_out_lng | decimal(10,7) | nullable, GPS longitude |
| completed_at | datetime | nullable |
| timestamps | | |

### commissions
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| user_id | bigint | FK |
| job_id | bigint | FK |
| type | enum | direct, override |
| amount | decimal(10,2) | |
| month | varchar | e.g. 2026-03 |
| status | enum | pending, approved, paid |
| paid_at | datetime | nullable |
| timestamps | | |

### points
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| user_id | bigint | FK |
| job_id | bigint | FK |
| points | int | |
| month | varchar | |
| timestamps | | |

### commission_rules
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| service_type | varchar | |
| therapist_commission | decimal(10,2) | Direct commission |
| leader_override | decimal(10,2) | Override commission |
| points_per_job | int | Points awarded |
| status | enum | active, inactive |
| timestamps | | |

### reward_tiers
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| title | varchar | e.g. Bronze, Silver, Gold |
| min_points | int | Minimum points to qualify |
| reward_description | text | |
| month | varchar | nullable (monthly reward) |
| status | enum | active, inactive |
| timestamps | | |

### sop_materials
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| title | varchar | |
| description | text | nullable |
| file_path | varchar | |
| uploaded_by | bigint | FK |
| timestamps | | |

### notifications
| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| user_id | bigint | FK |
| title | varchar | |
| message | text | |
| type | varchar | job_assigned, commission, announcement |
| read_at | datetime | nullable |
| timestamps | | |

---

## 7-Day Sprint Plan

---

### DAY 1 — Foundation & Auth
**Goal**: Project skeleton + authentication + role system

- [✅] Laravel 12 fresh install
- [✅] Configure `.env` (MySQL, Redis, app settings)
- [✅] Install packages:
  - `laravel/breeze`
  - `spatie/laravel-permission`
  - `yajra/laravel-datatables`
  - `barryvdh/laravel-dompdf`
- [✅] Create all migrations (users, jobs, commissions, points, commission_rules, reward_tiers, sop_materials, notifications)
- [✅] Run migrations
- [✅] Setup Spatie roles: `hq`, `leader`, `therapist`
- [✅] Create seeders (default HQ admin, sample data)
- [✅] Auth middleware for role-based access
- [✅] Login / Register pages (Bootstrap 5 themed)
- [✅] Role-based redirect after login:
  - HQ → `/hq/dashboard`
  - Leader → `/leader/dashboard`
  - Therapist → `/therapist/dashboard`
- [✅] Base layout template (sidebar + topbar + content area)

**Deliverable**: Login works, roles assigned, redirects correctly. ✅

---

### DAY 2 — User Management
**Goal**: HQ can manage Leaders, Leaders can manage Therapists

**HQ Admin Panel:**
- [✅] Leaders list (DataTable) — name, state, team size, status
- [✅] Create Leader form — name, email, phone, IC, state, district, KKM cert
- [✅] Edit / Activate / Deactivate Leader
- [✅] View Leader's team (therapists under them)

**Leader Panel:**
- [✅] Therapist list (DataTable) — own team only
- [✅] Register new Therapist — name, email, phone, IC, bank details
- [✅] Edit / Activate / Deactivate Therapist
- [✅] Therapist auto-linked to Leader via `leader_id`

**Therapist:**
- [✅] Self-registration (optional, approved by Leader)
- [✅] Profile page — view & edit own details

**Deliverable**: Full user CRUD for all 3 roles. ✅

---

### DAY 3 — Job Management
**Goal**: Full job lifecycle — create, assign, track, complete

**HQ:**
- [✅] View all jobs nationwide (DataTable with filters)
- [✅] Create job (assign to any leader/therapist)
- [✅] Filter by state, status, date range

**Leader:**
- [✅] Create job for client
- [✅] Assign job to therapist in their team
- [✅] View all team jobs
- [✅] Reassign job if needed

**Therapist:**
- [✅] View assigned jobs
- [✅] Accept job → status changes to `accepted`
- [✅] **Check In** → arrives at customer location, captures GPS + timestamp → status changes to `checked_in`
- [✅] **Check Out** → finishes job & leaves, captures GPS + timestamp → status changes to `completed`, triggers commission calc
- [✅] Add job notes/feedback

**Check-In / Check-Out Feature:**
- [✅] Check In button (only visible when status = `accepted`)
  - Capture GPS coordinates (browser geolocation API)
  - Record `checked_in_at` timestamp + `checked_in_lat/lng`
  - Status → `checked_in`
  - Notify Leader that therapist has arrived
- [✅] Check Out button (only visible when status = `checked_in`)
  - Capture GPS coordinates
  - Record `checked_out_at` timestamp + `checked_out_lat/lng`
  - Auto-calculate duration (checked_out_at - checked_in_at)
  - Status → `completed`
  - Triggers commission calculation + points award
  - Notify Leader that job is completed
- [✅] HQ & Leader can view check-in/out timestamps + GPS on job detail
- [✅] Job timeline shows: Assigned → Accepted → Checked In → Checked Out (Completed)

**Job Features:**
- [✅] Auto-generate job code (e.g. `JOB-20260301-001`)
- [✅] Job status badge (color-coded: pending=gray, accepted=blue, checked_in=orange, completed=green, cancelled=red)
- [✅] Job detail page with full timeline (including check-in/out timestamps & GPS)
- [✅] Duration display (time spent at customer location)

**Deliverable**: Jobs can be created, assigned, checked in/out, and completed with GPS tracking and status timeline. ✅

---

### DAY 4 — Commission Engine
**Goal**: Auto-calculate commissions when job is completed

**Commission Rules (HQ):**
- [✅] CRUD for commission rules per service type
- [✅] Set therapist direct commission amount
- [✅] Set leader override commission amount
- [✅] Set points per job

**Auto-Calculation Logic:**
- [✅] On job status → `completed`:
  - Create therapist commission record (direct)
  - Create leader commission record (override)
  - Award points to therapist
  - Award points to leader (if applicable)
- [✅] Use inline calculation in Therapist\ServiceJobController (Day 3)

**Commission Views:**
- [✅] HQ: View all commissions, filter by month, approve/mark as paid
- [✅] Leader: View own + team commissions
- [✅] Therapist: View own commissions (monthly breakdown)

**Monthly Summary:**
- [✅] Group by month
- [✅] Total earned, total pending, total paid
- [✅] Per-job breakdown

**Deliverable**: Commissions auto-calculate on job completion, viewable by all roles. ✅

---

### DAY 5 — Dashboards
**Goal**: Role-specific dashboards with stats and charts

**HQ Dashboard:**
- [✅] Total Leaders / Therapists / Active agents
- [✅] Total Jobs (this month / all time)
- [✅] Total Commission distributed (this month)
- [✅] Jobs by status (pie chart)
- [✅] Jobs by state (bar chart)
- [✅] Top 10 Therapists (by jobs completed)
- [✅] Recent activity feed

**Leader Dashboard:**
- [✅] Team size
- [✅] Team jobs this month
- [✅] Team commission this month
- [✅] Own override commission
- [✅] Team performance chart
- [✅] Pending jobs to assign

**Therapist Dashboard:**
- [✅] Total jobs completed (this month / all time)
- [✅] Total commission earned (this month / all time)
- [✅] Total points accumulated
- [✅] Current ranking position
- [✅] Upcoming assigned jobs
- [✅] Monthly earnings chart (bar chart, last 6 months)

**Deliverable**: All 3 dashboards functional with live data and charts. ✅

---

### DAY 6 — Points, Leaderboard, PDF & SOP
**Goal**: Gamification + document features

**Points System:**
- [✅] Points auto-awarded on job completion (from commission_rules)
- [✅] Monthly points summary per user
- [✅] Lifetime points tracker

**Reward Tiers (HQ):**
- [✅] CRUD reward tiers (Bronze, Silver, Gold, etc.)
- [✅] Auto-assign tier badge based on points
- [✅] Display tier on therapist profile

**Leaderboard:**
- [✅] Nationwide ranking (top therapists by points, this month)
- [✅] State-level ranking
- [✅] Leader team ranking
- [✅] Display rank # on therapist dashboard

**PDF Monthly Statement:**
- [✅] Therapist can download monthly commission statement
- [✅] PDF includes: name, month, job list, commission per job, total
- [✅] Leader can download team summary
- [✅] HQ can download full report

**SOP & Training Materials:**
- [✅] HQ upload files (PDF, images, videos)
- [✅] Categorize materials
- [✅] Leaders & Therapists can view/download
- [✅] Access from sidebar menu

**Deliverable**: Points, leaderboard, PDF export, and SOP module complete. ✅

---

### DAY 7 — Notifications, Polish & Deploy
**Goal**: Final features, bug fixes, deploy to production

**Notifications:**
- [✅] Database notifications (Laravel built-in)
- [✅] Notify therapist when job assigned
- [✅] Notify leader when job completed by therapist
- [✅] Notify on commission approval
- [✅] Bell icon with unread count in topbar
- [✅] Mark as read / mark all as read

**Polish & Testing:**
- [✅] Test all flows: register → assign job → complete → commission → PDF
- [✅] Fix UI issues
- [✅] Mobile responsive check
- [✅] Form validation on all forms
- [✅] Error handling
- [✅] Security review (middleware, auth gates)

**Deploy to VPS:**
- [ ] Push code to VPS (`/var/www/confinement-wellness`)
- [ ] Configure Nginx server block
- [ ] Setup `.env` for production
- [ ] Run migrations on production
- [ ] Setup Laravel queue worker (systemd)
- [ ] Setup Laravel scheduler (cron)
- [ ] SSL certificate (Certbot)
- [ ] Final live testing

**Deliverable**: System is LIVE on production.

---

## Post-Launch (Phase 2 — Future)

- [ ] WhatsApp/Telegram notification integration
- [✅] PWA (Progressive Web App) — service worker, manifest, offline support, push notifications
- [✅] Client booking portal
- [ ] Attendance & check-in system (GPS)
- [ ] Advanced analytics & reporting
- [ ] Multi-language support (BM/EN)
- [ ] Payroll integration
- [ ] Inventory management (products used per session)

---

## Key Questions (Answered)

1. **Commission structure** — HQ/Admin/Owner can dynamically set commission values per service type, including leader override amounts. All configurable from admin panel.
2. **Point rules** — HQ/Admin/Owner can dynamically set points per job and reward thresholds. All configurable from admin panel.
3. **Service types** — Will be set and managed by HQ through the admin panel (dynamic CRUD).
4. **Domain** — TBD (pending confirmation with client).
5. **Branding** — Modern clinic theme, professional design. Logo and company name TBD from client.
6. **Develop locally or directly on VPS?** — Development: Local environment. Production: DigitalOcean VPS.

---

## Notes

- All dates in Malaysian timezone (Asia/Kuala_Lumpur)
- Currency in MYR (RM)
- UI language: Bahasa Malaysia (primary) / English
- Mobile-responsive is mandatory (therapists will use phone)
