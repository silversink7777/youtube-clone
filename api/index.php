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
            // VideoSeederを直接実行
            $kernel->call('db:seed', ['--class' => 'VideoSeeder', '--force' => true]);
            $kernel->call('db:seed', ['--class' => 'CommentSeeder', '--force' => true]);
            error_log("VideoSeeder and CommentSeeder executed successfully");
        }
        
        error_log("Database initialized successfully");
    } catch (Exception $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        // 空のデータベースファイルを作成して継続
        touch('/tmp/database.sqlite');
    }
}

// デバッグ用: データベース状態確認エンドポイント
if (isset($_GET['debug']) && $_GET['debug'] === 'db') {
    try {
        require_once __DIR__.'/../vendor/autoload.php';
        $app = require_once __DIR__.'/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        
        $pdo = new PDO('sqlite:/tmp/database.sqlite');
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM tbl_videos");
        $videoCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        header('Content-Type: application/json');
        echo json_encode([
            'database_exists' => file_exists('/tmp/database.sqlite'),
            'video_count' => $videoCount,
            'user_count' => $userCount,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// データベース強制リセットエンドポイント
if (isset($_GET['reset_db']) && $_GET['reset_db'] === 'true') {
    try {
        // データベースファイルを削除
        if (file_exists('/tmp/database.sqlite')) {
            unlink('/tmp/database.sqlite');
        }
        
        // 新しいデータベースファイルを作成
        touch('/tmp/database.sqlite');
        chmod('/tmp/database.sqlite', 0664);
        
        require_once __DIR__.'/../vendor/autoload.php';
        $app = require_once __DIR__.'/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        
        // マイグレーション実行
        $kernel->call('migrate', ['--force' => true]);
        
        // シーダー実行
        $kernel->call('db:seed', ['--class' => 'VideoSeeder', '--force' => true]);
        $kernel->call('db:seed', ['--class' => 'CommentSeeder', '--force' => true]);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Database reset and seeded successfully',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

// 強制シーダー実行エンドポイント
if (isset($_GET['force_seed']) && $_GET['force_seed'] === 'true') {
    try {
        require_once __DIR__.'/../vendor/autoload.php';
        $app = require_once __DIR__.'/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        
        // 個別にシーダーを実行
        $kernel->call('db:seed', ['--class' => 'VideoSeeder', '--force' => true]);
        $kernel->call('db:seed', ['--class' => 'CommentSeeder', '--force' => true]);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Database seeded successfully',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

require __DIR__.'/../public/index.php';
