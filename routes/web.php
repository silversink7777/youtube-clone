<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\VideoController;

Route::get('/', [VideoController::class, 'index']);

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// API routes for videos
Route::prefix('api')->group(function () {
    Route::get('/videos', [VideoController::class, 'apiIndex']);
    Route::get('/videos/category/{category}', [VideoController::class, 'byCategory']);
    Route::get('/videos/search', [VideoController::class, 'search']);
});

// Individual video route