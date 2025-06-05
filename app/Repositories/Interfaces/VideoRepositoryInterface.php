<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface VideoRepositoryInterface
{
    /**
     * 公開された動画を取得
     */
    public function getPublishedVideos(?string $category = null, ?string $search = null): Collection;

    /**
     * ニュース動画を取得
     */
    public function getNewsVideos(int $limit = 3): Collection;

    /**
     * 動画をIDで取得
     */
    public function findById(int $id);

    /**
     * ユーザーの動画を取得
     */
    public function getUserVideos(int $userId): Collection;

    /**
     * カテゴリ別動画を取得
     */
    public function getVideosByCategory(string $category): Collection;

    /**
     * 検索による動画取得
     */
    public function searchVideos(string $query): Collection;
} 