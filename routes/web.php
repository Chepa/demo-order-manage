<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    $file = storage_path('api-docs/api-docs.json');

    abort_unless(file_exists($file), 404, 'Swagger docs not generated. Run `php artisan l5-swagger:generate`.');

    return response()->file($file, [
        'Content-Type' => 'application/json',
    ]);
})->name('l5-swagger.default.docs');

