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


Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // プロジェクト関連のルート
    Route::resource('projects', ProjectController::class);

    // タスク関連のルート
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

    // 見積書関連のルート
    Route::resource('quotes', QuoteController::class);
    // 見積書PDF生成ルートをここに追加
    Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'generatePdf'])->name('quotes.generatePdf');
    // 見積書PDFダウンロードルート
    Route::get('/quotes/{quote}/download-pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.downloadPdf');


    // 納品関連のルート
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');

    // 請求書関連のルート
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

    // 経費関連のルート
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');

    // 経費ステータス関連のルート
    Route::get('/expense-statuses', [ExpenseStatusController::class, 'index'])->name('expense_statuses.index');

    // クライアント関連のルート
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');

    // ユーザー関連のルート
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // ロール関連のルート
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

    // pdf
    Route::get('/quotes/{quote}/pdf-mpdf', [QuoteController::class, 'downloadPdf'])
    ->name('quotes.generatePdf');



});

require __DIR__.'/auth.php';
