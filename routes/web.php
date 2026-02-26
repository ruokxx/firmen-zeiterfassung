<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "web" middleware group. Make something great! | */

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/month/{year}/{month}', [\App\Http\Controllers\MonthController::class , 'show'])->middleware(['auth', 'verified'])->name('month.show');
Route::post('/month/import-holidays', [\App\Http\Controllers\MonthController::class , 'importHolidays'])->middleware(['auth', 'verified'])->name('month.import-holidays');
Route::post('/month/remark', [\App\Http\Controllers\MonthController::class , 'saveRemark'])->middleware(['auth', 'verified'])->name('month.remark');
Route::get('/help', function () {
    $faqs = \App\Models\Faq::where('is_active', true)->orderBy('order')->get();
    return view('help', compact('faqs'));
})->middleware(['auth', 'verified'])->name('help');
Route::middleware('auth')->group(function () {
    Route::get('/workday/{date}', [\App\Http\Controllers\WorkDayController::class , 'edit'])->name('workday.edit');
    Route::post('/workday/ajax-save', [\App\Http\Controllers\WorkDayController::class , 'saveAjax'])->name('workday.save-ajax');
    Route::post('/workday/{date}/status', [\App\Http\Controllers\WorkDayController::class , 'setStatus'])->name('workday.set-status');
    Route::put('/workday/{workDay}', [\App\Http\Controllers\WorkDayController::class , 'update'])->name('workday.update');
    Route::delete('/workday/{date}/reset', [\App\Http\Controllers\WorkDayController::class , 'destroy'])->name('workday.reset');
    Route::get('/report/download', [\App\Http\Controllers\ReportController::class , 'download'])->name('report.download');
    Route::post('/report/send', [\App\Http\Controllers\ReportController::class , 'sendEmail'])->name('report.send');

    // Admin Setup
    Route::get('/admin/setup-account', [\App\Http\Controllers\SetupAccountController::class , 'create'])->name('admin.setup.create');
    Route::post('/admin/setup-account', [\App\Http\Controllers\SetupAccountController::class , 'store'])->name('admin.setup.store');

    Route::get('/admin', [\App\Http\Controllers\AdminController::class , 'index'])->name('admin.dashboard');
    Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\AdminController::class , 'editUser'])->name('admin.users.edit');
    Route::patch('/admin/users/{user}/update', [\App\Http\Controllers\AdminController::class , 'updateUser'])->name('admin.users.update');
    Route::post('/admin/users/{user}/role', [\App\Http\Controllers\AdminController::class , 'updateRole'])->name('admin.users.update-role');
    Route::get('/admin/users/{user}/email', [\App\Http\Controllers\AdminController::class , 'email'])->name('admin.users.email');
    Route::post('/admin/users/{user}/email', [\App\Http\Controllers\AdminController::class , 'sendEmail'])->name('admin.users.email.send');
    Route::post('/admin/users/{user}/approve', [\App\Http\Controllers\AdminController::class , 'approve'])->name('admin.users.approve');
    Route::post('/admin/users/{user}/vacation-days', [\App\Http\Controllers\AdminController::class , 'updateVacationDays'])->name('admin.users.update-vacation-days');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\AdminController::class , 'destroy'])->name('admin.users.delete'); // Soft delete or hard delete

    // Admin Settings
    Route::get('/admin/settings', [\App\Http\Controllers\AdminSettingsController::class , 'index'])->name('admin.settings');
    Route::post('/admin/settings', [\App\Http\Controllers\AdminSettingsController::class , 'update'])->name('admin.settings.update');
    Route::post('/admin/settings/vacation', [\App\Http\Controllers\AdminSettingsController::class , 'updateVacation'])->name('admin.settings.update-vacation');
    Route::post('/admin/settings/email-template', [\App\Http\Controllers\AdminSettingsController::class , 'updateEmailTemplate'])->name('admin.settings.update-email-template');
    Route::post('/admin/settings/test-material-email', [\App\Http\Controllers\AdminSettingsController::class , 'testMaterialEmail'])->name('admin.settings.test-material-email');

    // Admin Documents
    Route::get('/admin/documents', [\App\Http\Controllers\AdminDocumentController::class , 'index'])->name('admin.documents.index');
    Route::get('/admin/documents/create', [\App\Http\Controllers\AdminDocumentController::class , 'create'])->name('admin.documents.create');
    Route::get('/admin/documents/create', [\App\Http\Controllers\AdminDocumentController::class , 'create'])->name('admin.documents.create');
    Route::post('/admin/documents', [\App\Http\Controllers\AdminDocumentController::class , 'store'])->name('admin.documents.store');

    // Admin Materials
    Route::get('/admin/materials', [\App\Http\Controllers\AdminMaterialController::class , 'index'])->name('admin.materials.index');
    Route::post('/admin/materials', [\App\Http\Controllers\AdminMaterialController::class , 'store'])->name('admin.materials.store');
    Route::delete('/admin/materials/{material}', [\App\Http\Controllers\AdminMaterialController::class , 'destroy'])->name('admin.materials.destroy');

    // Admin Construction Site Search
    Route::get('/admin/construction-sites', [\App\Http\Controllers\AdminConstructionSiteController::class , 'index'])->name('admin.construction-sites.index');

    // Database Backup
    // Database Backup
    Route::post('/admin/backup/generate', [\App\Http\Controllers\AdminSettingsController::class , 'generateBackup'])->name('admin.backup.generate');
    Route::get('/admin/backup/download/{filename}', [\App\Http\Controllers\AdminSettingsController::class , 'downloadBackup'])->name('admin.backup.download');
    Route::post('/admin/backup/restore', [\App\Http\Controllers\AdminSettingsController::class , 'restoreBackup'])->name('admin.backup.restore');
    Route::delete('/admin/backup/{filename}', [\App\Http\Controllers\AdminSettingsController::class , 'deleteBackup'])->name('admin.backup.delete');

    // Admin FAQs
    Route::resource('admin/faqs', \App\Http\Controllers\AdminFaqController::class)->names('admin.faqs')->except(['show']);

    // Trello OAuth
    Route::get('/auth/trello/redirect', [\App\Http\Controllers\TrelloController::class , 'redirect'])->name('auth.trello.redirect');
    Route::get('/auth/trello/callback', [\App\Http\Controllers\TrelloController::class , 'callback'])->name('auth.trello.callback');

    // User Documents
    Route::patch('/profile/documents/{document}', [\App\Http\Controllers\UserDocumentController::class , 'update'])->name('user.documents.update');
    Route::get('/profile/documents/{document}/download', [\App\Http\Controllers\UserDocumentController::class , 'download'])->name('user.documents.download');
    Route::get('/profile/documents/{document}/download-response', [\App\Http\Controllers\UserDocumentController::class , 'downloadResponse'])->name('user.documents.download-response');

    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
    Route::delete('/profile/month', [ProfileController::class , 'clearMonth'])->name('profile.clear-month');

    // Material Orders
    Route::get('/material-orders', [\App\Http\Controllers\MaterialOrderController::class , 'index'])->name('material-orders.index');
    Route::post('/material-orders', [\App\Http\Controllers\MaterialOrderController::class , 'store'])->name('material-orders.store');
    Route::patch('/material-orders/{order}/toggle', [\App\Http\Controllers\MaterialOrderController::class , 'toggle'])->name('material-orders.toggle');
    Route::patch('/material-orders/{order}', [\App\Http\Controllers\MaterialOrderController::class , 'update'])->name('material-orders.update');
    Route::delete('/material-orders/{order}', [\App\Http\Controllers\MaterialOrderController::class , 'destroy'])->name('material-orders.destroy');

    // Materials (Lager & Verwaltung)
    Route::get('/materials', [\App\Http\Controllers\MaterialController::class , 'index'])->name('materials.index');
    Route::post('/materials/{material}/transaction', [\App\Http\Controllers\MaterialController::class , 'transaction'])->name('materials.transaction');
    Route::get('/admin/materials/manage', [\App\Http\Controllers\MaterialController::class , 'manage'])->name('materials.manage');
    Route::post('/admin/materials/manage', [\App\Http\Controllers\MaterialController::class , 'store'])->name('materials.store');
    Route::put('/admin/materials/manage/{material}', [\App\Http\Controllers\MaterialController::class , 'update'])->name('materials.update');
    Route::delete('/admin/materials/manage/{material}', [\App\Http\Controllers\MaterialController::class , 'destroy'])->name('materials.destroy');

    // Material Categories
    Route::post('/admin/material-categories', [\App\Http\Controllers\MaterialCategoryController::class , 'store'])->name('material-categories.store');
    Route::put('/admin/material-categories/{category}', [\App\Http\Controllers\MaterialCategoryController::class , 'update'])->name('material-categories.update');
    Route::delete('/admin/material-categories/{category}', [\App\Http\Controllers\MaterialCategoryController::class , 'destroy'])->name('material-categories.destroy');
    Route::get('/admin/materials/stats', [\App\Http\Controllers\MaterialController::class , 'stats'])->name('materials.stats');
    Route::delete('/admin/materials/stats/clear', [\App\Http\Controllers\MaterialController::class , 'clearStats'])->name('materials.stats.clear');
    Route::post('/admin/materials/settings', [\App\Http\Controllers\MaterialController::class , 'updateSettings'])->name('materials.settings.update');
    Route::get('/admin/materials/settings/test-email', [\App\Http\Controllers\MaterialController::class , 'sendTestEmail'])->name('materials.settings.test-email');
});

require __DIR__ . '/auth.php';
