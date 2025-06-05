<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CommentController;

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
Route::get('/watch/{video}', [VideoController::class, 'show'])->name('video.show');

// Comment routes (API)
Route::prefix('api/videos/{video}/comments')->group(function () {
    Route::get('/', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');
    Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');
    Route::patch('/{comment}/pin', [CommentController::class, 'togglePin'])->name('comments.pin')->middleware('auth');
    Route::post('/{comment}/like', [CommentController::class, 'toggleLike'])->name('comments.like')->middleware('auth');
    Route::post('/{comment}/dislike', [CommentController::class, 'toggleDislike'])->name('comments.dislike')->middleware('auth');
});
