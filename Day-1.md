# Day 1 — Foundation & Auth

## Context
Fresh Laravel 12 project for Confinement & Wellness Agent Management System. No existing code — starting from scratch. This plan covers the full Day 1 sprint: project scaffold, database, auth, roles, and base layout.

## Pre-requisites
- PHP 8.3, Composer, MySQL, Node.js available locally
- MySQL database `confinement_wellness` created

---

## Step 1: Laravel 12 Fresh Install
- `composer create-project laravel/laravel .` in project root
- Configure `.env`: DB name, timezone `Asia/Kuala_Lumpur`, app name

## Step 2: Install Packages
```
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require spatie/laravel-permission
composer require yajra/laravel-datatables
composer require barryvdh/laravel-dompdf
```
- Publish Spatie config + migration: `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`

## Step 3: Migrations (7 tables + modify users)
Rename `jobs` → `service_jobs` to avoid Laravel queue conflict.
Use Laravel's built-in notifications (Day 7) instead of custom table.

| # | Migration | Key Columns |
|---|-----------|-------------|
| 1 | Modify `users` | +phone, ic_number, role(enum), leader_id(FK), state, district, kkm_cert_no, bank_name, bank_account, status(enum), profile_photo |
| 2 | `service_jobs` | job_code, client_name/phone/address, state, district, service_type, job_date/time, assigned_by/to(FK), status(enum), notes, checked_in/out fields (datetime+lat/lng), completed_at |
| 3 | `commissions` | user_id, job_id(FK→service_jobs), type(enum), amount, month, status(enum), paid_at |
| 4 | `points` | user_id, job_id(FK→service_jobs), points, month |
| 5 | `commission_rules` | service_type, therapist_commission, leader_override, points_per_job, status(enum) |
| 6 | `reward_tiers` | title, min_points, reward_description, month, status(enum) |
| 7 | `sop_materials` | title, description, file_path, uploaded_by(FK) |

## Step 4: Models (7 models + modify User)
- **User** — add fillable, role enum, relationships (leader, therapists, jobs, commissions, points), Spatie `HasRoles` trait
- **ServiceJob** — relationships to assigner, assignee, commissions, points
- **Commission** — belongs to user, service_job
- **Point** — belongs to user, service_job
- **CommissionRule** — standalone
- **RewardTier** — standalone
- **SopMaterial** — belongs to uploader

## Step 5: Seeder
- Create 3 Spatie roles: `hq`, `leader`, `therapist`
- Seed HQ admin: `admin@confinement.com` / `password`
- Seed sample Leader + 2 Therapists
- Seed sample commission rules (3 service types)
- Seed sample reward tiers (Bronze/Silver/Gold)

## Step 6: Auth & Middleware
- Breeze provides login/register views (we'll restyle to Bootstrap 5)
- Create `RoleMiddleware` or use Spatie's built-in `role` middleware
- Register middleware in `bootstrap/app.php`
- Role-based redirect after login in `AuthenticatedSessionController` or via `RouteServiceProvider`:
  - hq → `/hq/dashboard`
  - leader → `/leader/dashboard`
  - therapist → `/therapist/dashboard`

## Step 7: Routes
```php
// routes/web.php
Route::middleware(['auth', 'role:hq'])->prefix('hq')->name('hq.')->group(fn() => ...);
Route::middleware(['auth', 'role:leader'])->prefix('leader')->name('leader.')->group(fn() => ...);
Route::middleware(['auth', 'role:therapist'])->prefix('therapist')->name('therapist.')->group(fn() => ...);
```
- Each group gets a `DashboardController@index` for now

## Step 8: Controllers
- `App\Http\Controllers\HQ\DashboardController`
- `App\Http\Controllers\Leader\DashboardController`
- `App\Http\Controllers\Therapist\DashboardController`
- Each returns its respective dashboard view

## Step 9: Base Layout (Bootstrap 5)
- `resources/views/layouts/app.blade.php` — main layout
  - Bootstrap 5 CDN + jQuery CDN + Font Awesome CDN
  - Top navbar (app name, user dropdown, notification bell placeholder, logout)
  - Collapsible sidebar (role-based menu items via `@role` directive)
  - Content area with `@yield('content')`
  - Footer
- Sidebar menu items per role:
  - **HQ**: Dashboard, Leaders, Therapists, Jobs, Commissions, Points, Commission Rules, Reward Tiers, SOP Materials, Notifications
  - **Leader**: Dashboard, My Team, Jobs, Commissions, Points, SOP Materials, Notifications
  - **Therapist**: Dashboard, My Jobs, Commissions, Points, Leaderboard, SOP Materials, Notifications
- Restyle Breeze auth views (login/register) to use Bootstrap 5 instead of Tailwind

## Step 10: Dashboard Placeholder Views
- `resources/views/hq/dashboard.blade.php`
- `resources/views/leader/dashboard.blade.php`
- `resources/views/therapist/dashboard.blade.php`
- Each extends layout, shows welcome message + role badge

---

## File Summary
| Action | Path |
|--------|------|
| Create | `database/migrations/*_add_fields_to_users_table.php` |
| Create | `database/migrations/*_create_service_jobs_table.php` |
| Create | `database/migrations/*_create_commissions_table.php` |
| Create | `database/migrations/*_create_points_table.php` |
| Create | `database/migrations/*_create_commission_rules_table.php` |
| Create | `database/migrations/*_create_reward_tiers_table.php` |
| Create | `database/migrations/*_create_sop_materials_table.php` |
| Modify | `app/Models/User.php` |
| Create | `app/Models/ServiceJob.php` |
| Create | `app/Models/Commission.php` |
| Create | `app/Models/Point.php` |
| Create | `app/Models/CommissionRule.php` |
| Create | `app/Models/RewardTier.php` |
| Create | `app/Models/SopMaterial.php` |
| Create | `database/seeders/DatabaseSeeder.php` (overwrite) |
| Create | `database/seeders/RoleSeeder.php` |
| Create | `app/Http/Controllers/HQ/DashboardController.php` |
| Create | `app/Http/Controllers/Leader/DashboardController.php` |
| Create | `app/Http/Controllers/Therapist/DashboardController.php` |
| Modify | `routes/web.php` |
| Modify | `bootstrap/app.php` (middleware) |
| Create | `resources/views/layouts/app.blade.php` |
| Create | `resources/views/hq/dashboard.blade.php` |
| Create | `resources/views/leader/dashboard.blade.php` |
| Create | `resources/views/therapist/dashboard.blade.php` |
| Modify | `resources/views/auth/login.blade.php` (Bootstrap restyle) |
| Modify | `resources/views/auth/register.blade.php` (Bootstrap restyle) |

---

## Verification
1. `php artisan migrate` — all tables created successfully
2. `php artisan db:seed` — roles + users seeded
3. Login as `admin@confinement.com` → redirected to `/hq/dashboard`
4. Login as seeded leader → redirected to `/leader/dashboard`
5. Login as seeded therapist → redirected to `/therapist/dashboard`
6. Sidebar shows correct menu items per role
7. Unauthorized access (e.g. therapist visiting `/hq/dashboard`) → 403
