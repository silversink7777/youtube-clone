<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use App\Repositories\VideoRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // VideoRepositoryの依存性注入設定
        $this->app->bind(VideoRepositoryInterface::class, VideoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vercel環境でのデータベーステーブル存在チェック
        if (config('database.default') === 'sqlite' && app()->environment('production')) {
            try {
                $this->ensureDatabaseTables();
            } catch (\Exception $e) {
                \Log::error('Database table check failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Vercel環境でデータベーステーブルの存在を確認する
     */
    private function ensureDatabaseTables(): void
    {
        $dbPath = config('database.connections.sqlite.database');
        
        if (!file_exists($dbPath)) {
            \Log::info('Database file does not exist, will be created automatically');
            return;
        }

        try {
            // 重要なテーブルの存在をチェック
            $tables = ['users', 'tbl_videos', 'migrations'];
            $pdo = new \PDO('sqlite:' . $dbPath);
            
            foreach ($tables as $table) {
                $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}'");
                if (!$stmt->fetch()) {
                    \Log::warning("Table {$table} does not exist in database");
                }
            }
        } catch (\Exception $e) {
            \Log::error('Database table check error: ' . $e->getMessage());
        }
    }
}
