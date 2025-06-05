<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('tbl_videos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->foreignId('parent_id')->nullable()->constrained('tbl_comments')->onDelete('cascade'); // 返信コメント用
            $table->integer('likes_count')->default(0);
            $table->boolean('is_pinned')->default(false); // チャンネル運営者によるピン留め
            $table->timestamps();

            // インデックス
            $table->index('video_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index(['video_id', 'created_at']);
        });
        
        // テーブルの文字セットとコレーションを明示的に設定
        DB::statement('ALTER TABLE tbl_comments CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_comments');
    }
};
