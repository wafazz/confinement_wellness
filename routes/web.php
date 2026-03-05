<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HQ\DashboardController as HQDashboardController;
use App\Http\Controllers\HQ\LeaderController;
use App\Http\Controllers\HQ\ServiceJobController as HQServiceJobController;
use App\Http\Controllers\HQ\TherapistController as HQTherapistController;
use App\Http\Controllers\HQ\CommissionRuleController;
use App\Http\Controllers\HQ\CommissionController as HQCommissionController;
use App\Http\Controllers\HQ\RewardTierController;
use App\Http\Controllers\HQ\PointController as HQPointController;
use App\Http\Controllers\HQ\SopMaterialController as HQSopMaterialController;
use App\Http\Controllers\HQ\BookingController as HQBookingController;
use App\Http\Controllers\Leader\DashboardController as LeaderDashboardController;
use App\Http\Controllers\Leader\TherapistController;
use App\Http\Controllers\Leader\ServiceJobController as LeaderServiceJobController;
use App\Http\Controllers\Leader\CommissionController as LeaderCommissionController;
use App\Http\Controllers\Leader\SopMaterialController as LeaderSopMaterialController;
use App\Http\Controllers\Leader\BookingController as LeaderBookingController;
use App\Http\Controllers\Therapist\DashboardController as TherapistDashboardController;
use App\Http\Controllers\Therapist\ProfileController as TherapistProfileController;
use App\Http\Controllers\Therapist\ServiceJobController as TherapistServiceJobController;
use App\Http\Controllers\Therapist\CommissionController as TherapistCommissionController;
use App\Http\Controllers\Therapist\PointController as TherapistPointController;
use App\Http\Controllers\Therapist\LeaderboardController;
use App\Http\Controllers\Therapist\SopMaterialController as TherapistSopMaterialController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\BookingController as PublicBookingController;
use App\Http\Controllers\Client\Auth\LoginController as ClientLoginController;
use App\Http\Controllers\Client\Auth\RegisterController as ClientRegisterController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\ReviewController as ClientReviewController;
use App\Http\Controllers\HQ\ReviewController as HQReviewController;
use Illuminate\Support\Facades\Route;

// ── Locale Switch ──
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ms'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

// ── Public Routes ──
Route::get('/', [LandingController::class, 'index'])->name('public.landing');
Route::get('/book', [PublicBookingController::class, 'create'])->name('public.booking.create');
Route::post('/book', [PublicBookingController::class, 'store'])->name('public.booking.store');
Route::get('/booking/confirmation/{code}', [PublicBookingController::class, 'confirmation'])->name('public.booking.confirmation');
Route::get('/api/therapists-by-state', [PublicBookingController::class, 'therapistsByState'])->name('public.booking.therapists');

// ── Client Auth Routes ──
Route::prefix('client')->name('client.')->group(function () {
    Route::get('/login', [ClientLoginController::class, 'create'])->name('login');
    Route::post('/login', [ClientLoginController::class, 'store'])->name('login.store');
    Route::get('/register', [ClientRegisterController::class, 'create'])->name('register');
    Route::post('/register', [ClientRegisterController::class, 'store'])->name('register.store');
});

// ── Client Portal Routes (auth.client) ──
Route::middleware('auth.client')->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [ClientBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [ClientBookingController::class, 'show'])->name('bookings.show');
    Route::get('/reviews', [ClientReviewController::class, 'index'])->name('reviews.index');
    Route::get('/jobs/{job}/review', [ClientReviewController::class, 'create'])->name('reviews.create');
    Route::post('/jobs/{job}/review', [ClientReviewController::class, 'store'])->name('reviews.store');
    Route::post('/logout', [ClientLoginController::class, 'destroy'])->name('logout');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->role) {
        'hq' => redirect()->route('hq.dashboard'),
        'leader' => redirect()->route('leader.dashboard'),
        'therapist' => redirect()->route('therapist.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware(['auth'])->name('dashboard');

// HQ Routes
Route::middleware(['auth', 'role:hq'])->prefix('hq')->name('hq.')->group(function () {
    Route::get('/dashboard', [HQDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/leaders', LeaderController::class);
    Route::patch('/leaders/{leader}/toggle-status', [LeaderController::class, 'toggleStatus'])->name('leaders.toggle-status');
    Route::get('/leaders/{leader}/team', [LeaderController::class, 'team'])->name('leaders.team');
    Route::get('/therapists', [HQTherapistController::class, 'index'])->name('therapists.index');
    Route::get('/therapists/{therapist}', [HQTherapistController::class, 'show'])->name('therapists.show');
    Route::patch('/therapists/{therapist}/toggle-status', [HQTherapistController::class, 'toggleStatus'])->name('therapists.toggle-status');
    Route::resource('/jobs', HQServiceJobController::class)->except(['destroy']);
    Route::patch('/jobs/{job}/cancel', [HQServiceJobController::class, 'cancel'])->name('jobs.cancel');
    Route::resource('/commission-rules', CommissionRuleController::class)->except(['show', 'destroy']);
    Route::patch('/commission-rules/{commission_rule}/toggle-status', [CommissionRuleController::class, 'toggleStatus'])->name('commission-rules.toggle-status');
    Route::get('/commissions', [HQCommissionController::class, 'index'])->name('commissions.index');
    Route::patch('/commissions/{commission}/approve', [HQCommissionController::class, 'approve'])->name('commissions.approve');
    Route::patch('/commissions/{commission}/mark-paid', [HQCommissionController::class, 'markPaid'])->name('commissions.mark-paid');
    Route::post('/commissions/bulk-approve', [HQCommissionController::class, 'bulkApprove'])->name('commissions.bulk-approve');
    Route::post('/commissions/bulk-paid', [HQCommissionController::class, 'bulkPaid'])->name('commissions.bulk-paid');
    Route::get('/commissions/download-pdf', [HQCommissionController::class, 'downloadPdf'])->name('commissions.download-pdf');
    Route::resource('/reward-tiers', RewardTierController::class)->except(['show', 'destroy']);
    Route::patch('/reward-tiers/{reward_tier}/toggle-status', [RewardTierController::class, 'toggleStatus'])->name('reward-tiers.toggle-status');
    Route::get('/points', [HQPointController::class, 'index'])->name('points.index');
    Route::resource('/sop-materials', HQSopMaterialController::class)->except(['show']);
    // Reviews
    Route::get('/reviews', [HQReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [HQReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/reject', [HQReviewController::class, 'reject'])->name('reviews.reject');
    // Bookings
    Route::get('/bookings', [HQBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [HQBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/approve', [HQBookingController::class, 'approve'])->name('bookings.approve');
    Route::patch('/bookings/{booking}/reject', [HQBookingController::class, 'reject'])->name('bookings.reject');
    Route::get('/bookings/{booking}/convert', [HQBookingController::class, 'convertForm'])->name('bookings.convert-form');
    Route::post('/bookings/{booking}/convert', [HQBookingController::class, 'convert'])->name('bookings.convert');
});

// Leader Routes
Route::middleware(['auth', 'role:leader'])->prefix('leader')->name('leader.')->group(function () {
    Route::get('/dashboard', [LeaderDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/therapists', TherapistController::class);
    Route::patch('/therapists/{therapist}/toggle-status', [TherapistController::class, 'toggleStatus'])->name('therapists.toggle-status');
    Route::resource('/jobs', LeaderServiceJobController::class)->except(['destroy']);
    Route::patch('/jobs/{job}/cancel', [LeaderServiceJobController::class, 'cancel'])->name('jobs.cancel');
    Route::patch('/jobs/{job}/check-in', [LeaderServiceJobController::class, 'checkIn'])->name('jobs.check-in');
    Route::patch('/jobs/{job}/check-out', [LeaderServiceJobController::class, 'checkOut'])->name('jobs.check-out');
    Route::patch('/jobs/{job}/accept', [LeaderServiceJobController::class, 'accept'])->name('jobs.accept');
    Route::patch('/jobs/{job}/wellness-check-in', [LeaderServiceJobController::class, 'wellnessCheckIn'])->name('jobs.wellness-check-in');
    Route::patch('/jobs/{job}/wellness-check-out', [LeaderServiceJobController::class, 'wellnessCheckOut'])->name('jobs.wellness-check-out');
    Route::patch('/jobs/{job}/daily-check-in', [LeaderServiceJobController::class, 'dailyCheckIn'])->name('jobs.daily-check-in');
    Route::patch('/jobs/{job}/daily-check-out', [LeaderServiceJobController::class, 'dailyCheckOut'])->name('jobs.daily-check-out');
    Route::post('/jobs/{job}/updates', [LeaderServiceJobController::class, 'postUpdate'])->name('jobs.post-update');
    Route::patch('/jobs/{job}/notes', [LeaderServiceJobController::class, 'addNotes'])->name('jobs.notes');
    Route::get('/commissions', [LeaderCommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/download-pdf', [LeaderCommissionController::class, 'downloadPdf'])->name('commissions.download-pdf');
    Route::get('/sop-materials', [LeaderSopMaterialController::class, 'index'])->name('sop-materials.index');
    // Bookings
    Route::get('/bookings', [LeaderBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [LeaderBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/approve', [LeaderBookingController::class, 'approve'])->name('bookings.approve');
    Route::patch('/bookings/{booking}/reject', [LeaderBookingController::class, 'reject'])->name('bookings.reject');
    Route::get('/bookings/{booking}/convert', [LeaderBookingController::class, 'convertForm'])->name('bookings.convert-form');
    Route::post('/bookings/{booking}/convert', [LeaderBookingController::class, 'convert'])->name('bookings.convert');
});

// Therapist Routes
Route::middleware(['auth', 'role:therapist'])->prefix('therapist')->name('therapist.')->group(function () {
    Route::get('/dashboard', [TherapistDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [TherapistProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [TherapistProfileController::class, 'update'])->name('profile.update');
    Route::get('/jobs', [TherapistServiceJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [TherapistServiceJobController::class, 'show'])->name('jobs.show');
    Route::patch('/jobs/{job}/accept', [TherapistServiceJobController::class, 'accept'])->name('jobs.accept');
    Route::patch('/jobs/{job}/check-in', [TherapistServiceJobController::class, 'checkIn'])->name('jobs.check-in');
    Route::patch('/jobs/{job}/check-out', [TherapistServiceJobController::class, 'checkOut'])->name('jobs.check-out');
    Route::patch('/jobs/{job}/daily-check-in', [TherapistServiceJobController::class, 'dailyCheckIn'])->name('jobs.daily-check-in');
    Route::patch('/jobs/{job}/daily-check-out', [TherapistServiceJobController::class, 'dailyCheckOut'])->name('jobs.daily-check-out');
    Route::patch('/jobs/{job}/notes', [TherapistServiceJobController::class, 'addNotes'])->name('jobs.notes');
    Route::post('/jobs/{job}/updates', [TherapistServiceJobController::class, 'postUpdate'])->name('jobs.post-update');
    Route::get('/commissions', [TherapistCommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/download-pdf', [TherapistCommissionController::class, 'downloadPdf'])->name('commissions.download-pdf');
    Route::get('/points', [TherapistPointController::class, 'index'])->name('points.index');
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/sop-materials', [TherapistSopMaterialController::class, 'index'])->name('sop-materials.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

require __DIR__.'/auth.php';
