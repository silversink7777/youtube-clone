<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use App\Repositories\VideoRepository;
use Illuminate\Support\Facades\File;

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
        // HTTPS強制設定（本番環境）
        if (app()->environment('production')) {
            \URL::forceScheme('https');
            
            // 環境変数から取得、または固定URL
            $assetUrl = env('ASSET_URL', 'https://youtube-clone-nine-sepia.vercel.app');
            \Illuminate\Support\Facades\URL::forceRootUrl($assetUrl);
            
            // アセットURLの強制HTTPS設定
            if ($assetUrl) {
                config(['app.asset_url' => $assetUrl]);
            }
        }

        // Vercel環境でのデータベーステーブル存在チェック
        if (config('database.default') === 'sqlite' && app()->environment('production')) {
            try {
                $this->ensureDatabaseTables();
            } catch (\Exception $e) {
                \Log::error('Database table check failed: ' . $e->getMessage());
            }
        }

        // Vercel環境でのViteマニフェスト自動生成
        if (app()->environment('production')) {
            $this->ensureViteManifest();
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

    /**
     * Vercel環境でViteマニフェストの存在を確認し、必要に応じて生成する
     */
    private function ensureViteManifest(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $buildDir = public_path('build');
        $assetsDir = public_path('build/assets');

        // ビルドディレクトリが存在しない場合は何もしない
        if (!is_dir($buildDir)) {
            \Log::warning('Build directory does not exist');
            return;
        }

        // マニフェストファイルが既に存在する場合は何もしない
        if (file_exists($manifestPath)) {
            return;
        }

        // アセットディレクトリが存在する場合、動的にマニフェストを生成
        if (is_dir($assetsDir)) {
            try {
                $this->generateViteManifest($manifestPath, $assetsDir);
                \Log::info('Vite manifest generated successfully');
            } catch (\Exception $e) {
                \Log::error('Failed to generate Vite manifest: ' . $e->getMessage());
            }
        }
    }

    /**
     * Viteマニフェストファイルを動的に生成する
     */
    private function generateViteManifest(string $manifestPath, string $assetsDir): void
    {
        $files = File::files($assetsDir);
        $manifest = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $relativePath = 'assets/' . $filename;

            // アプリケーションのメインファイルを特定
            if (str_contains($filename, 'app-') && str_ends_with($filename, '.js')) {
                $manifest['resources/js/app.js'] = [
                    'file' => $relativePath,
                    'src' => 'resources/js/app.js',
                    'isEntry' => true
                ];
            }

            if (str_contains($filename, 'app-') && str_ends_with($filename, '.css')) {
                $manifest['resources/css/app.css'] = [
                    'file' => $relativePath,
                    'src' => 'resources/css/app.css',
                    'isEntry' => true
                ];
            }

            // その他のJSファイル（Vue component files等）
            if (str_ends_with($filename, '.js') && !str_contains($filename, 'app-')) {
                $componentName = str_replace(['.js', '_'], ['', '-'], $filename);
                $manifest['_' . $componentName] = [
                    'file' => $relativePath
                ];
            }
        }

        // マニフェストファイルを作成
        if (!empty($manifest)) {
            File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}
