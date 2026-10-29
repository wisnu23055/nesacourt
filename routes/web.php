<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

/*
|--------------------------------------------------------------------------
| Autentikasi (F-01 s/d F-04)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

/*
|--------------------------------------------------------------------------
| Kelompok 2: Sisi Pembeli (F-05 s/d F-14)
|--------------------------------------------------------------------------
*/
// Menu & katalog publik
Route::view('/katalog', 'buyer.catalog')->name('buyer.catalog');
Route::view('/rekomendasi', 'buyer.recommendation')->name('buyer.recommendation');
Route::view('/keranjang', 'buyer.cart')->name('buyer.cart');

// Area pembeli terautentikasi
Route::middleware(['auth', 'role:buyer,admin'])->group(function () {
    Route::view('/dashboard', 'buyer.dashboard')->name('buyer.dashboard');
    Route::view('/checkout', 'buyer.checkout')->name('buyer.checkout');
    Route::view('/pesanan/bayar', 'buyer.payment')->name('buyer.payment');
    Route::view('/pesanan/status', 'buyer.order-status')->name('buyer.order-status');
    Route::view('/riwayat', 'buyer.history')->name('buyer.history');
    Route::view('/profil', 'buyer.profile')->name('buyer.profile');
});

/*
|--------------------------------------------------------------------------
| Kelompok 3: Sisi Penjual / Pemilik Stan (F-15 s/d F-24)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:seller,admin'])->prefix('penjual')->name('seller.')->group(function () {
    Route::view('/dashboard', 'seller.dashboard')->name('dashboard');
    Route::view('/menu', 'seller.menu.index')->name('menu.index');
    Route::view('/menu/create', 'seller.menu.form')->name('menu.create');
    Route::view('/stok', 'seller.stock')->name('stock');
    Route::view('/qris', 'seller.qris')->name('qris');
    Route::view('/pesanan', 'seller.orders.index')->name('orders.index');
    Route::view('/pesanan/detail', 'seller.orders.detail')->name('orders.detail');
    Route::view('/laporan', 'seller.reports')->name('reports');
    Route::view('/prediksi', 'seller.forecast')->name('forecast');
    Route::view('/profil', 'seller.profile')->name('profile');
});

/*
|--------------------------------------------------------------------------
| Kelompok 4: Sisi Admin / Pengelola Food Court (F-25 s/d F-31)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/stan', 'admin.stalls')->name('stalls');
    Route::view('/menu', 'admin.menu')->name('menu');
    Route::view('/pesanan', 'admin.orders')->name('orders');
    Route::view('/pesanan/detail', 'admin.order-detail')->name('order-detail');
    Route::view('/laporan', 'admin.reports')->name('reports');
    Route::view('/log', 'admin.logs')->name('logs');
});
