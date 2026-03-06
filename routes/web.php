<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectKeywordFlagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExpenseStatusController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\SummaryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// 認証済みユーザーのみ
Route::middleware('auth')->group(function () {

    // ダッシュボード・プロフィール
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/attendance/fix-missing', [AttendanceController::class, 'fixMissing'])->name('attendance.fix-missing');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 業務管理系（プロジェクト、タスク、見積、納品、請求、経費、顧客）
    Route::resource('projects', ProjectController::class);
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('keyword_flags', ProjectKeywordFlagController::class);
    });
    Route::get('/admin/keyword_flags/match', [ProjectKeywordFlagController::class, 'match'])->name('admin.keyword_flags.match');
    Route::patch('/projects/{project}/checklists/{checklist}/toggle-status', [ProjectController::class, 'toggleChecklistStatus'])->name('projects.checklists.toggleStatus');
    Route::patch('/projects/{project}/checklists/{checklist}/update-link', [ProjectController::class, 'updateChecklistLink']);
    Route::post('/quotes/{quote}/downloadPdfMpdf', [QuoteController::class, 'downloadPdfMpdf'])->name('quotes.downloadPdfMpdf');

    Route::resource('tasks', TaskController::class);
    Route::get('/tasks/weekly', [TaskController::class, 'weeklyIndex'])->name('tasks.weekly');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    Route::resource('quotes', QuoteController::class);
    Route::get('/quotes/{quote}/pdf-mpdf', [QuoteController::class, 'downloadPdf'])->name('quotes.downloadPdfMpdf');
    Route::patch('/quotes/{quote}/toggle-status', [QuoteController::class, 'toggleStatus'])->name('quotes.toggleStatus');

    Route::resource('deliveries', DeliveryController::class);
    Route::get('/deliveries/{delivery}/pdf-mpdf', [DeliveryController::class, 'downloadPdf'])->name('deliveries.downloadPdfMpdf');
    Route::patch('/deliveries/{delivery}/toggle-status', [DeliveryController::class, 'toggleStatus'])->name('deliveries.toggleStatus');

    Route::resource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/toggle-status', [InvoiceController::class, 'toggleStatus'])->name('invoices.toggleStatus');
    Route::get('/invoices/{invoice}/pdf-mpdf', [InvoiceController::class, 'downloadPdfMpdf'])->name('invoices.downloadPdfMpdf');

    Route::resource('expenses', ExpenseController::class);
    Route::resource('expense-statuses', ExpenseStatusController::class);
    Route::patch('/expenses/{expense}/status', [ExpenseController::class, 'updateStatus'])->name('expenses.updateStatus');

    Route::resource('clients', ClientController::class);

    // --- 勤怠管理 (SummaryController側で 11 or developer の制限をかけます) ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/summary', [SummaryController::class, 'index'])->name('summary.index');
        Route::get('/summary/user/{user}', [SummaryController::class, 'show'])->name('summary.show');
        Route::get('/summary/user/{user}/download', [SummaryController::class, 'download'])->name('summary.download');
    });

    // --- 管理者機能 (UserController/RoleController/LocationController側で developer のみの制限をかけます) ---
    Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('locations', LocationController::class);
    });

    // 打刻関連
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/recent', [AttendanceController::class, 'recent'])->name('attendance.recent');
});

require __DIR__.'/auth.php';

Route::get('/check-db', function() {
    return "Laravel routing is working.";
});

