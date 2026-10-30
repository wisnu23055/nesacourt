<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerController;
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
Route::get('/katalog', [BuyerController::class, 'catalog'])->name('buyer.catalog');
Route::get('/rekomendasi', [BuyerController::class, 'recommendation'])->name('buyer.recommendation');
Route::get('/keranjang', [BuyerController::class, 'cart'])->name('buyer.cart');

// Area pembeli terautentikasi
Route::middleware(['auth', 'role:buyer,admin'])->group(function () {
    Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('buyer.dashboard');
    Route::get('/checkout', [BuyerController::class, 'checkout'])->name('buyer.checkout');
    Route::post('/checkout', [BuyerController::class, 'storeOrder'])->name('buyer.checkout.store');
    Route::get('/pesanan/bayar', [BuyerController::class, 'payment'])->name('buyer.payment');
    Route::post('/pesanan/{order_number}/konfirmasi-bayar', [BuyerController::class, 'confirmPayment'])->name('buyer.payment.confirm');
    Route::get('/pesanan/status', [BuyerController::class, 'orderStatus'])->name('buyer.order-status');
    Route::get('/riwayat', [BuyerController::class, 'history'])->name('buyer.history');
    Route::get('/profil', [BuyerController::class, 'profile'])->name('buyer.profile');
    Route::post('/profil', [BuyerController::class, 'updateProfile'])->name('buyer.profile.update');
    Route::post('/profil/password', [BuyerController::class, 'updatePassword'])->name('buyer.profile.password');
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
