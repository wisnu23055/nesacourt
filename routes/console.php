<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ponytail: jalankan auto-cancel pesanan kedaluwarsa tiap menit (Blueprint B-15)
Schedule::command('orders:cancel-expired')->everyMinute();
