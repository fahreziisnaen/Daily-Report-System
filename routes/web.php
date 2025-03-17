<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ReportController;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\Admin\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::group(['middleware' => ['auth', 'can:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/rekap/export/{user}', [RekapController::class, 'export'])->name('rekap.export');
        Route::resource('projects', ProjectController::class);
        Route::get('/api/active-projects', [ProjectController::class, 'getActiveProjects']);
    });

    // Employee routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/{report}/export', [ReportController::class, 'export'])->name('reports.export');

    // API routes for search
    Route::middleware('auth')->group(function () {
        Route::get('/api/search-employees', function (Request $request) {
            return User::role('employee')
                ->where('name', 'LIKE', '%' . $request->q . '%')
                ->pluck('name');
        });

        Route::get('/api/search-locations', function (Request $request) {
            return Report::where('location', 'LIKE', '%' . $request->q . '%')
                ->distinct()
                ->pluck('location');
        });

        Route::get('/api/search-projects', function (Request $request) {
            return Report::where('project_code', 'LIKE', '%' . $request->q . '%')
                ->distinct()
                ->pluck('project_code');
        });

        // Tambahkan route baru untuk mengambil project aktif
        Route::get('/api/active-projects', [ProjectController::class, 'getActiveProjects']);
    });
});

require __DIR__.'/auth.php';
