<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('tbl_comments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // 同じユーザーが同じコメントに複数回いいねできないように制約
            $table->unique(['comment_id', 'user_id']);
            
            // パフォーマンス用のインデックス
            $table->index('comment_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_comment_likes');
    }
};
