# Session Memory - Confinement & Wellness

> Last updated: 2026-02-27 Session 9

## Session Context
- **Project**: Confinement & Women Wellness Agent Management System
- **Profile**: `~/Desktop/MemoryCore Project/Projects/03-codex-lure.md`
- **Branch**: N/A (no git init yet)
- **Status**: active
- **Focus**: Post-sprint polish — Profile page fix, photo upload, HQ Therapists list

## Current Tasks
- [x] Day 1: Foundation & Auth
- [x] Day 2: User Management + Dashboard Design + Sidebar Redesign
- [x] Day 3: Job Management (full lifecycle with GPS)
- [x] Day 4: Commission Engine (rules CRUD, approve/pay, monthly views)
- [x] Day 5: Dashboards & Charts (HQ full rebuild, Leader/Therapist updates)
- [x] Day 6: Points, Leaderboard, PDF, SOP
- [x] Day 7: Notifications & Polish (deploy is manual on VPS)
- [x] Post-sprint: Profile page rebuilt (Bootstrap 5), photo upload, HQ Therapists

## Working Memory
### Active Context
- Profile page (`/profile`) rebuilt from Breeze Tailwind → Bootstrap 5 layout
- Profile photo upload: store in `storage/app/public/profile-photos/`, show in sidebar + profile page
- HQ TherapistController: index (DataTable), show (with performance stats), toggleStatus
- HQ Therapists sidebar link now points to real route
- ~120 total routes

### Decisions Made
- Profile photo stored in `public/profile-photos/` disk, max 2MB
- HQ Therapists is read-only + toggle status (no create/edit — leaders manage their own therapists)
- Profile page shows role-appropriate fields (bank details for leader/therapist, KKM cert for leaders)

## Recent Changes
| File | Change | Status |
|---|---|---|
| profile/edit.blade.php | Rebuilt from Breeze Tailwind → Bootstrap 5 with photo upload | done |
| ProfileController.php | Added photo upload/remove, expanded fields | done |
| ProfileUpdateRequest.php | Added phone, state, district, bank, kkm, photo validation | done |
| HQ/TherapistController.php | New — index (DataTable), show (stats), toggleStatus | done |
| hq/therapists/index.blade.php | New — DataTable with leader name column | done |
| hq/therapists/show.blade.php | New — profile details + performance sidebar | done |
| layouts/app.blade.php | Sidebar avatar shows profile photo, HQ Therapists link | done |
| routes/web.php | +3 HQ therapist routes | done |

## Session Recap
### What Was Done
- Fixed blank `/profile` page (was using Breeze `<x-app-layout>`, rebuilt with Bootstrap 5)
- Added profile photo upload with preview, remove checkbox, storage cleanup
- Sidebar avatar shows real photo when uploaded (Leader/Therapist warm sidebar)
- Built HQ Therapists feature (was placeholder `#` link): DataTable + show + toggle
- Confirmed service types are already dynamically managed via Commission Rules

### Where We Left Off
- All 7-day sprint features + post-sprint polish complete
- Remaining: VPS deployment (manual by Fakrul)

### Key Context
- ~120 total routes
- All test accounts: password = `password`
- Profile photos: `storage/app/public/profile-photos/`
- HQ Therapists: read-only list + toggle status (leaders manage own team)
