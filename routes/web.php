<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function() {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('home');
    }
    return view('welcome');
})->name('landing');

// Portal Otentikasi Admin
Route::get('/admin/login', [LoginController::class, 'index'])->name('admin.login');
Route::get('/login/admin', function() {
    return redirect()->route('admin.login');
});
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// Portal Otentikasi User Umum
Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserAuthController::class, 'login'])->name('login.post');
Route::get('/register', [UserAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserAuthController::class, 'register'])->name('register.post');
Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

// Rute Baca Semua Notifikasi
Route::post('/notifications/read-all', function() {
    \App\Models\Notification::where('is_read', false)->update(['is_read' => true]);
    return redirect()->back();
})->name('notifications.read-all');

Route::middleware(['role:user,admin'])->group(function() {
    Route::get('/home', [UserAuthController::class, 'beranda'])->name('home');
    Route::get('/profile', [UserAuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserAuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/publications/{publication}', [UserAuthController::class, 'showPublication'])->name('user.publications.show');
});

// AREA ADMINISTRATOR (Dilindungi role:admin dengan prefix /admin)
Route::prefix('admin')->middleware(['role:admin'])->group(function() {
    // Dashboard Admin & Statistik
    Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('admin.dashboard');

    // Pengelolaan Publikasi
    Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');
    Route::get('/publications/create', [PublicationController::class, 'create'])->name('publications.create');
    Route::post('/publications', [PublicationController::class, 'store'])->name('publications.store');
    Route::get('/publications/{publication}', [PublicationController::class, 'show'])->name('publications.show');
    Route::get('/publications/{publication}/edit', [PublicationController::class, 'edit'])->name('publications.edit');
    Route::put('/publications/{publication}', [PublicationController::class, 'update'])->name('publications.update');
    Route::post('/publications/{publication}/extract', [PublicationController::class, 'extract'])->name('publications.extract');
    Route::post('/publications/{publication}/save-extracted-text', [PublicationController::class, 'saveExtractedText'])->name('publications.saveExtractedText');
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');

    // Daftar Ringkasan
    Route::get('/summaries', [PublicationController::class, 'summaries'])->name('admin.summaries');

    // Halaman Aktivitas Sistem
    Route::get('/activities', [PublicationController::class, 'activities'])->name('activities.index');
    Route::delete('/activities/clear', [PublicationController::class, 'clearActivities'])->name('activities.clear');

    // Pengaturan Akun Admin
    Route::get('/account-settings', [LoginController::class, 'accountSettings'])->name('account.settings');
    Route::post('/account-settings', [LoginController::class, 'updateAccountSettings'])->name('account.settings.update');
    Route::post('/account-settings/password', [LoginController::class, 'updatePassword'])->name('account.settings.password');

    // Manajemen User CRUD
    Route::resource('users', UserController::class)->except(['show']);
});
