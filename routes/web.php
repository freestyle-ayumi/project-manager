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
use App\Http\Controllers\ExpenseStatusController; // ExpenseStatusControllerをインポート

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// トップページ
Route::get('/', function () {
    return view('welcome');
});

// 認証済みユーザーのみアクセス可能なルートグループ
Route::middleware('auth')->group(function () {
    // ダッシュボードルート
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // プロフィール関連のルート
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // プロジェクト関連のルート
    Route::resource('projects', ProjectController::class);

    // タスク関連のルート
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

    // 見積書関連のルート
    // PDF系の個別ルート
    Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'generatePdf'])->name('quotes.generatePdf');
    Route::get('/quotes/{quote}/download-pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.downloadPdf');
    // 注意: 以前のdownloadPdfとgeneratePdfが同じメソッドを指している場合、重複を避けるか、用途を明確にしてください。
    // 例: Route::get('/quotes/{quote}/pdf-mpdf', [QuoteController::class, 'downloadPdf'])->name('quotes.downloadPdf');
    // こちらは既存のdownloadPdfルートと重複するため、コメントアウトまたは削除を検討してください。

    // 見積書リソースルート (必ず一番最後に書く)
    Route::resource('quotes', QuoteController::class);

    // 納品関連のルート
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');

    // 請求書関連のルート
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

    // 経費関連のルート
    // ★ここをresourceルートに変更しました
    Route::resource('expenses', ExpenseController::class);

    // 経費ステータス関連のルート
    Route::resource('expense-statuses', ExpenseStatusController::class); // resourceルートを追加

    // クライアント関連のルート
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');

    // ユーザー関連のルート
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // ロール関連のルート
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
});

// Laravel Breezeなどの認証パッケージが提供する認証ルート
require __DIR__.'/auth.php';
