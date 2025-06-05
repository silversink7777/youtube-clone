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
    
    // 動画アップロード関連ルート
    Route::get('/upload', [VideoController::class, 'create'])->name('video.create');
    Route::post('/upload', [VideoController::class, 'store'])->name('video.store');
    Route::get('/manage-videos', [VideoController::class, 'manage'])->name('video.manage');
    Route::get('/videos/{video}/edit', [VideoController::class, 'edit'])->name('video.edit');
    Route::put('/videos/{video}', [VideoController::class, 'update'])->name('video.update');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('video.destroy');
    Route::get('/upload/progress', [VideoController::class, 'getUploadProgress'])->name('video.progress');
});

// Video proxy route for external URLs (to fix CORS issues)
Route::get('/video-proxy', [VideoController::class, 'proxy'])->name('video.proxy');

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
