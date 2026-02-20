<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/ai-request', function () {
    define('BASE_URL', 'https://api.flickr.com/services');

    $apiKey= $_ENV['API_KEY'];
    
    $queryString = http_build_query([
        'x-goog-api-key' => $apiKey,
        'content_type' => 1,
        'format' => 'php_serial',
        'media' => 'photos',
        'method' => 'flickr.photos.search',
        'per_page' => 10,
        'safe_search' => 1,
    ]);
    $requestUri = sprintf(
        '%s/rest/?%s',
        BASE_URL,
        $queryString
    );
    $fp = fopen($requestUri, 'r');
    return response('Hello World', 200)
        ->header('Content-Type', 'text/plain');
});

require __DIR__.'/settings.php';