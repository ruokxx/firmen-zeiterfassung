<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "web" middleware group. Make something great! | */

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/month/{year}/{month}', [\App\Http\Controllers\MonthController::class , 'show'])->middleware(['auth', 'verified'])->name('month.show');

Route::middleware('auth')->group(function () {
    Route::get('/workday/{date}', [\App\Http\Controllers\WorkDayController::class , 'edit'])->name('workday.edit');
    Route::put('/workday/{workDay}', [\App\Http\Controllers\WorkDayController::class , 'update'])->name('workday.update');
    Route::get('/report/download', [\App\Http\Controllers\ReportController::class , 'download'])->name('report.download');
    Route::post('/report/send', [\App\Http\Controllers\ReportController::class , 'sendEmail'])->name('report.send');

    // Admin Setup
    Route::get('/admin/setup-account', [\App\Http\Controllers\SetupAccountController::class , 'create'])->name('admin.setup.create');
    Route::post('/admin/setup-account', [\App\Http\Controllers\SetupAccountController::class , 'store'])->name('admin.setup.store');

    Route::get('/admin', [\App\Http\Controllers\AdminController::class , 'index'])->name('admin.dashboard');
    Route::post('/admin/users/{user}/toggle-admin', [\App\Http\Controllers\AdminController::class , 'toggleAdmin'])->name('admin.users.toggle');
    Route::post('/admin/users/{user}/approve', [\App\Http\Controllers\AdminController::class , 'approve'])->name('admin.users.approve');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\AdminController::class , 'destroy'])->name('admin.users.delete'); // Soft delete or hard delete

    // Admin Settings
    Route::get('/admin/settings', [\App\Http\Controllers\AdminSettingsController::class , 'index'])->name('admin.settings');
    Route::post('/admin/settings', [\App\Http\Controllers\AdminSettingsController::class , 'update'])->name('admin.settings.update');

    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
