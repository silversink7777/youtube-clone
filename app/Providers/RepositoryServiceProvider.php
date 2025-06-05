<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\CommentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 