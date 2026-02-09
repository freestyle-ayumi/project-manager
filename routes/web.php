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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// トップページ
Route::get('/', function () {
    return view('welcome');
});

// 認証済みユーザーのみアクセス可能
Route::middleware('auth')->group(function () {

    // ダッシュボード
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware(['auth'])
        ->name('dashboard');

    // プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // プロジェクト
    Route::resource('projects', ProjectController::class);

    // プロジェクトキーワード
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('keyword_flags', ProjectKeywordFlagController::class);
    });
    // キーワードテンプレートのマッチ検索（AJAX）
    Route::get('/admin/keyword_flags/match', [App\Http\Controllers\ProjectKeywordFlagController::class, 'match'])->name('admin.keyword_flags.match');
    Route::patch('/projects/{project}/checklists/{checklist}/toggle-status', [ProjectController::class, 'toggleChecklistStatus'])
    ->name('projects.checklists.toggleStatus');
    Route::patch('/projects/{project}/checklists/{checklist}/update-link', [ProjectController::class, 'updateChecklistLink']);
    Route::post('/quotes/{quote}/downloadPdfMpdf', [QuoteController::class, 'downloadPdfMpdf'])->name('quotes.downloadPdfMpdf');


    // タスク
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/weekly', [TaskController::class, 'weeklyIndex'])->name('tasks.weekly');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');


    // 見積書
    Route::resource('quotes', QuoteController::class);
    Route::get('/quotes/{quote}/pdf-mpdf', [QuoteController::class, 'downloadPdf'])->name('quotes.downloadPdfMpdf');
    Route::patch('/quotes/{quote}/toggle-status', [App\Http\Controllers\QuoteController::class, 'toggleStatus'])->name('quotes.toggleStatus');

    // 納品書
    Route::resource('deliveries', DeliveryController::class);
    Route::get('/deliveries/{delivery}/pdf-mpdf', [DeliveryController::class, 'downloadPdf'])->name('deliveries.downloadPdfMpdf');
    Route::patch('/deliveries/{delivery}/toggle-status', [DeliveryController::class, 'toggleStatus'])->name('deliveries.toggleStatus');

    // 請求書
    Route::resource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/toggle-status', [InvoiceController::class, 'toggleStatus'])->name('invoices.toggleStatus');
    Route::get('/invoices/{invoice}/pdf-mpdf', [InvoiceController::class, 'downloadPdfMpdf'])->name('invoices.downloadPdfMpdf');

    // 経費
    Route::resource('expenses', ExpenseController::class);
    Route::patch('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');

    // 経費ステータス
    Route::resource('expense-statuses', ExpenseStatusController::class);
    Route::patch('/expenses/{expense}/status', [ExpenseController::class, 'updateStatus'])->name('expenses.updateStatus');

    // 顧客
    Route::resource('clients', ClientController::class);
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

    // ユーザー管理（一覧・編集・更新・削除のみ）
    Route::resource('users', UserController::class)->except(['create', 'store', 'show']);

    // ロール管理
    Route::prefix('roles')->name('roles.')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/create', [RoleController::class, 'create'])->name('create'); // ←必要
    Route::post('/', [RoleController::class, 'store'])->name('store');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // ロケーション管理
    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
        Route::resource('locations', LocationController::class);
    });
    Route::post('/attendance', [App\Http\Controllers\AttendanceController::class, 'store'])
        ->name('attendance.store')
        ->middleware('auth');

    Route::get('/attendance/recent', [App\Http\Controllers\AttendanceController::class, 'recent'])
        ->name('attendance.recent')
        ->middleware('auth');
});

// 認証ルート
require __DIR__.'/auth.php';

Route::get('/check-db', function() {
    return "Laravel routing is working.";
});

