<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

/*
|--------------------------------------------------------------------------
| Kelompok 2: Sisi Pembeli (F-05 s/d F-14)
|--------------------------------------------------------------------------
*/
Route::view('/dashboard', 'buyer.dashboard')->name('buyer.dashboard');
Route::view('/katalog', 'buyer.catalog')->name('buyer.catalog');
Route::view('/rekomendasi', 'buyer.recommendation')->name('buyer.recommendation');
Route::view('/keranjang', 'buyer.cart')->name('buyer.cart');
Route::view('/checkout', 'buyer.checkout')->name('buyer.checkout');
Route::view('/pesanan/bayar', 'buyer.payment')->name('buyer.payment');
Route::view('/pesanan/status', 'buyer.order-status')->name('buyer.order-status');
Route::view('/riwayat', 'buyer.history')->name('buyer.history');
Route::view('/profil', 'buyer.profile')->name('buyer.profile');

/*
|--------------------------------------------------------------------------
| Kelompok 3: Sisi Penjual / Pemilik Stan (F-15 s/d F-24)
|--------------------------------------------------------------------------
*/
Route::view('/penjual/dashboard', 'seller.dashboard')->name('seller.dashboard');
Route::view('/penjual/menu', 'seller.menu.index')->name('seller.menu.index');
Route::view('/penjual/menu/create', 'seller.menu.form')->name('seller.menu.create');
Route::view('/penjual/stok', 'seller.stock')->name('seller.stock');
Route::view('/penjual/qris', 'seller.qris')->name('seller.qris');
Route::view('/penjual/pesanan', 'seller.orders.index')->name('seller.orders.index');
Route::view('/penjual/pesanan/detail', 'seller.orders.detail')->name('seller.orders.detail');
Route::view('/penjual/laporan', 'seller.reports')->name('seller.reports');
Route::view('/penjual/prediksi', 'seller.forecast')->name('seller.forecast');
Route::view('/penjual/profil', 'seller.profile')->name('seller.profile');

/*
|--------------------------------------------------------------------------
| Kelompok 4: Sisi Admin / Pengelola Food Court (F-25 s/d F-31)
|--------------------------------------------------------------------------
*/
Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
Route::view('/admin/stan', 'admin.stalls')->name('admin.stalls');
Route::view('/admin/menu', 'admin.menu')->name('admin.menu');
Route::view('/admin/pesanan', 'admin.orders')->name('admin.orders');
Route::view('/admin/pesanan/detail', 'admin.order-detail')->name('admin.order-detail');
Route::view('/admin/laporan', 'admin.reports')->name('admin.reports');
Route::view('/admin/log', 'admin.logs')->name('admin.logs');

