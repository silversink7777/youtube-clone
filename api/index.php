<?php
/**
* Here is the serverless function entry
* for deployment with Vercel.
*/

// Vercel環境でのデータベース初期化
if (!file_exists('/tmp/database.sqlite')) {
    try {
        // SQLiteファイルを作成
        touch('/tmp/database.sqlite');
        chmod('/tmp/database.sqlite', 0664);
        
        // Laravelアプリケーションを初期化
        require_once __DIR__.'/../vendor/autoload.php';
        $app = require_once __DIR__.'/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        
        // マイグレーションを実行
        $kernel->call('migrate', ['--force' => true]);
        
        // シーダーの実行（デモンストレーション用）
        $shouldSeed = $_ENV['DB_SEED'] ?? 'true';
        if ($shouldSeed === 'true') {
            $kernel->call('db:seed', ['--force' => true]);
        }
        
        error_log("Database initialized successfully");
    } catch (Exception $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        // 空のデータベースファイルを作成して継続
        touch('/tmp/database.sqlite');
    }
}

require __DIR__.'/../public/index.php';
