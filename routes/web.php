<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReceivableController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ======================== テスト ========================
//伝票入力画面
Route::get('/receivable', function () {
    return view('invoice.receivable'); // resources/views/receivable.blade.php
});

// パスワード変更画面
Route::get('/test-password-change', function () {
    return view('auth.change_password');
});

// Welcomeページ
Route::get('/', function () {
    return redirect()->route('login');
});

// ======================== 認証系 ========================
// ログイン画面表示
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// ログイン処理
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// ダッシュボード（一般ユーザー用）
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// ======================== パスワード変更 ========================
Route::get('/password/change', [PasswordController::class, 'showChangeForm'])
    ->middleware('auth')
    ->name('password.change');

Route::post('/password/change', [PasswordController::class, 'update'])
    ->middleware('auth')
    ->name('password.update');

// ======================== 一般ユーザー系 ========================

Route::get('/dashboard', function () {
    return view('auth.menu');
})->middleware('auth')->name('dashboard');

// ======================== 管理者系 ========================
Route::prefix('admin')->middleware('auth')->group(function () {
    // 管理者ダッシュボード
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');


    // ユーザー管理
    Route::get('/users', [UserAdminController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserAdminController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserAdminController::class, 'store'])->name('admin.users.store');

    // レポート管理
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');

    // システム設定 
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // 操作ログ ←これを追加
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

// ======================== adminからのログアウト ========================
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    // ログイン画面へリダイレクトしつつメッセージを渡す
    return redirect()->route('login')->with('status', 'ログアウトしました');
})->name('logout');

// ======================== メニューから各種伝票入力へ ========================
// 売掛伝票入力画面
Route::get('/invoice/receivable', [InvoiceController::class, 'receivable'])
    ->middleware('auth')
    ->name('invoice.receivable');

Route::resource('receivables', ReceivableController::class);

// ======================== 伝票入力画面操作 ========================

Route::get('/auth/menu', function () {
    return view('auth.menu');
})->name('menu');

