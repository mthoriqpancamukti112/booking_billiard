<?php

use App\Http\Controllers\Api\MidtransCallbackController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MejaBilliardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WaitingListController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/data-pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::put('/pelanggan/{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{pelanggan}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
    Route::get('/meja-billiard', [MejaBilliardController::class, 'index'])->name('meja_billiard.index');
    Route::get('/meja-billiard/create', [MejaBilliardController::class, 'create'])->name('meja_billiard.create')->middleware('prevent-cache');
    Route::post('/meja-billiard', [MejaBilliardController::class, 'store'])->name('meja_billiard.store');
    Route::get('/meja-billiard/{mejaBilliard}/edit', [MejaBilliardController::class, 'edit'])->name('meja_billiard.edit');
    Route::put('/meja-billiard/{mejaBilliard}', [MejaBilliardController::class, 'update'])->name('meja_billiard.update');
    Route::delete('/meja-billiard/{mejaBilliard}', [MejaBilliardController::class, 'destroy'])->name('meja_billiard.destroy');
    Route::get('/data-booking', [BookingController::class, 'adminIndex'])->name('admin.booking.index');
    // Route::post('/bookings/{booking_id}/cancel', [BookingController::class, 'adminCancel'])->name('booking.cancel');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/booking-meja', [BookingController::class, 'index'])->name('booking_meja.index')->middleware('prevent-cache');
    Route::post('/booking-meja', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/join-waiting-list', [BookingController::class, 'joinWaitingList'])->name('waitinglist.join');
    Route::get('/riwayat-booking', [BookingController::class, 'history'])->name('booking.history');
    Route::get('/booking/pay/{encrypted_id}', [BookingController::class, 'pay'])->name('booking.pay')->middleware('prevent-cache');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/login', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.auth');
Route::get('/register', [RegisterController::class, 'index'])->name('register.index');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
