<?php

use Illuminate\Support\Facades\Route;

// Frontend Routes
Route::get("/", [\App\Http\Controllers\Frontend\HomeController::class, "index"])->name("home");

// Villa Routes
Route::get("/villas", [\App\Http\Controllers\Frontend\VillaController::class, "index"])->name("villas.index");
Route::get("/villas/{id}", [\App\Http\Controllers\Frontend\VillaController::class, "show"])->name("villas.show");

// Booking Routes
Route::middleware(["auth"])->group(function () {
    Route::get("/bookings/create/{villaId}", [\App\Http\Controllers\Frontend\BookingController::class, "create"])->name("bookings.create");
    Route::post("/bookings/{villaId}", [\App\Http\Controllers\Frontend\BookingController::class, "store"])->name("bookings.store");
    Route::get("/bookings", [\App\Http\Controllers\Frontend\BookingController::class, "index"])->name("bookings.index");
    Route::get("/bookings/{booking}", [\App\Http\Controllers\Frontend\BookingController::class, "show"])->name("bookings.show");
});

// User Profile Routes
Route::middleware(["auth"])->group(function () {
    Route::get("/profile", [\App\Http\Controllers\Frontend\UserController::class, "profile"])->name("profile");
    Route::post("/profile", [\App\Http\Controllers\Frontend\UserController::class, "update"])->name("profile.update");
});

// Admin Routes
Route::prefix("admin")->middleware(["auth", "admin"])->group(function () {
    Route::get("/dashboard", [\App\Http\Controllers\Admin\DashboardController::class, "index"])->name("admin.dashboard");
    
    Route::resource("villas", \App\Http\Controllers\Admin\VillaController::class)->names("admin.villas");
    Route::resource("bookings", \App\Http\Controllers\Admin\BookingController::class)->names("admin.bookings");
    Route::get("/reports", [\App\Http\Controllers\Admin\ReportController::class, "index"])->name("admin.reports");
});

require __DIR__."/auth.php";
