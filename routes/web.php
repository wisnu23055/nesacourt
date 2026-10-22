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

