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
        Schema::table('users', function (Blueprint $table) {
            $table->string('channel_name')->nullable()->after('name');
            $table->text('channel_description')->nullable()->after('channel_name');
            $table->bigInteger('subscriber_count')->default(0)->after('channel_description');
            $table->bigInteger('total_views')->default(0)->after('subscriber_count');
            $table->integer('video_count')->default(0)->after('total_views');
            $table->timestamp('channel_created_at')->nullable()->after('video_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'channel_name',
                'channel_description',
                'subscriber_count',
                'total_views',
                'video_count',
                'channel_created_at'
            ]);
        });
    }
};
