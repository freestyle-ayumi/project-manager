<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ExpenseStatusController;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // プロジェクト
    Route::resource('projects', ProjectController::class);

    // タスク
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/weekly', [TaskController::class, 'weeklyIndex'])->name('tasks.weekly');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::resource('tasks', TaskController::class);

    // 見積書
    Route::get('/quotes/{quote}/pdf-mpdf', [QuoteController::class, 'downloadPdf'])->name('quotes.downloadPdfMpdf');
    Route::resource('quotes', QuoteController::class);

    // 納品書
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');

    // 請求書
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

    // 経費
    Route::resource('expenses', ExpenseController::class);

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

});

// 認証ルート
require __DIR__.'/auth.php';

Route::get('/check-db', function() {
    return "Laravel routing is working.";
});

