<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobAlertController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminJobController;
use Illuminate\Support\Facades\Route;

// Cron webhook — called by external cron service (cron-job.org) every hour
Route::get('/cron/scrape/{token}', function (string $token) {
    if ($token !== config('app.cron_token')) {
        abort(403);
    }
    Illuminate\Support\Facades\Artisan::call('jobs:scrape');
    return response()->json(['status' => 'ok', 'output' => Illuminate\Support\Facades\Artisan::output()]);
});

// Public routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/jobs', [JobListingController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{jobListing}', [JobListingController::class, 'show'])->name('jobs.show');

// Job alert subscription (public)
Route::post('/job-alerts', [JobAlertController::class, 'store'])->name('job-alerts.store');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::post('/bookmarks/{jobListing}', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('jobs', AdminJobController::class)->parameters(['jobs' => 'jobListing']);
    Route::patch('/jobs/{jobListing}/approve', [AdminJobController::class, 'approve'])->name('jobs.approve');
    Route::patch('/jobs/{jobListing}/reject', [AdminJobController::class, 'reject'])->name('jobs.reject');
});
