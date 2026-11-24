<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-detect base path: cPanel (/home/nesacourtmyid675/laravel) atau Lokal (..)
$laravelBase = is_dir(__DIR__.'/../laravel') ? __DIR__.'/../laravel' : __DIR__.'/..';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravelBase.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravelBase.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $laravelBase.'/bootstrap/app.php';

// Arahkan public_path() ke public_html saat berjalan di cPanel
if (is_dir(__DIR__.'/../laravel')) {
    $app->usePublicPath(__DIR__);
}

$app->handleRequest(Request::capture());
