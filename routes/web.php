<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeSettingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\DiagnosticsController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\VillaController;
use App\Http\Controllers\HomeBackgroundController;
use App\Http\Controllers\PublicPaymentProofController;
use App\Http\Controllers\PublicVillaImageController;
use Illuminate\Support\Facades\Route;

Route::get('/diagnostics', DiagnosticsController::class)
    ->name('diagnostics');

Route::get('/villa-images/{villaImage}', [PublicVillaImageController::class, 'show'])
    ->name('villa-images.show');
Route::get('/home-backgrounds/{type}', HomeBackgroundController::class)
    ->name('home-background.show');

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Villa Routes
Route::get('/villas', [VillaController::class, 'index'])->name('villas.index');
Route::get('/villas/{id}', [VillaController::class, 'show'])->name('villas.show');

// Booking Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment-proofs/{payment}', [PublicPaymentProofController::class, 'show'])->name('payment-proofs.show');

    Route::get('/bookings/create/{villaId}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings/{villaId}', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/upload_payment', [BookingController::class, 'uploadPaymentProof'])->name('bookings.upload_payment');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// User Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::put('/profile', [UserController::class, 'update']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('villas', App\Http\Controllers\Admin\VillaController::class)
        ->names('admin.villas')
        ->except(['show']);
    Route::resource('bookings', App\Http\Controllers\Admin\BookingController::class)
        ->names('admin.bookings')
        ->only(['index', 'show']);
    Route::post('/bookings/{booking}/verify', [App\Http\Controllers\Admin\BookingController::class, 'verifyPayment'])->name('admin.bookings.verify');
    Route::post('/bookings/{booking}/reject', [App\Http\Controllers\Admin\BookingController::class, 'rejectPayment'])->name('admin.bookings.reject');
    Route::post('/bookings/{booking}/process_refund', [App\Http\Controllers\Admin\BookingController::class, 'processRefund'])->name('admin.bookings.process_refund');
    Route::post('/bookings/{booking}/update_status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('admin.bookings.update_status');
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
    Route::get('/settings/home', [HomeSettingController::class, 'edit'])->name('admin.settings.home');
    Route::put('/settings/home', [HomeSettingController::class, 'update'])->name('admin.settings.home.update');
});

require __DIR__.'/auth.php';
