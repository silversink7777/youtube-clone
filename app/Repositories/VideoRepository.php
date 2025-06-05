<?php

namespace App\Repositories;

use App\Repositories\Interfaces\VideoRepositoryInterface;
use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;

class VideoRepository implements VideoRepositoryInterface
{
    protected $model;

    public function __construct(Video $model)
    {
        $this->model = $model;
    }

    /**
     * 公開された動画を取得
     */
    public function getPublishedVideos(?string $category = null, ?string $search = null): Collection
    {
        $query = $this->model->with('user')
            ->published()
            ->orderBy('published_at', 'desc');

        // カテゴリフィルタ
        if ($category && $category !== 'all') {
            $query->byCategory($category);
        }

        // 検索フィルタ
        if ($search) {
            $query->search($search);
        }

        return $query->get();
    }

    /**
     * ニュース動画を取得
     */
    public function getNewsVideos(int $limit = 3): Collection
    {
        return $this->model->with('user')
            ->published()
            ->where('category', 'news')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 動画をIDで取得
     */
    public function findById(int $id)
    {
        return $this->model->with('user')->find($id);
    }

    /**
     * 動画詳細情報を取得
     */
    public function getVideoWithDetails(int $id): ?Video
    {
        return $this->model->with('user')->find($id);
    }

    /**
     * ユーザーの動画を取得
     */
    public function getUserVideos(int $userId): Collection
    {
        return $this->model->with('user')
            ->where('user_id', $userId)
            ->published()
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * カテゴリ別動画を取得
     */
    public function getVideosByCategory(string $category): Collection
    {
        return $this->model->with('user')
            ->published()
            ->byCategory($category)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * 検索による動画取得
     */
    public function searchVideos(string $query): Collection
    {
        return $this->model->with('user')
            ->published()
            ->search($query)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * 外部URLかどうかを判定
     */
    private function isExternalUrl(?string $url): bool
    {
        if (!$url) {
            return false;
        }

        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
    }

    /**
     * 動画URLを適切に処理
     */
    private function getVideoUrl(?string $videoUrl): ?string
    {
        if (!$videoUrl) {
            return null;
        }

        // 外部URLの場合はそのまま返す
        if ($this->isExternalUrl($videoUrl)) {
            return $videoUrl;
        }

        // ローカルファイルの場合はストレージパスを追加
        return asset('storage/' . $videoUrl);
    }

    /**
     * サムネイルURLを適切に処理
     */
    private function getThumbnailUrl(?string $thumbnailPath): ?string
    {
        if (!$thumbnailPath) {
            return null;
        }

        // 外部URLの場合はそのまま返す
        if ($this->isExternalUrl($thumbnailPath)) {
            return $thumbnailPath;
        }

        // ローカルファイルの場合はストレージパスを追加
        return asset('storage/' . $thumbnailPath);
    }

    /**
     * 動画データを配列形式に変換
     */
    public function transformToArray(Video $video): array
    {
        $channelName = $video->user->channel_name ?? $video->user->name;
        $channelInitial = mb_substr($channelName, 0, 1);
        
        $colors = [
            'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 
            'bg-purple-500', 'bg-pink-500', 'bg-indigo-500',
            'bg-red-500', 'bg-orange-500', 'bg-teal-500'
        ];
        $channelColor = $colors[$video->user_id % count($colors)];

        return [
            'id' => $video->id,
            'title' => $video->title,
            'description' => $video->description,
            'thumbnail' => $this->getThumbnailUrl($video->thumbnail),
            'video_url' => $this->getVideoUrl($video->video_url),
            'duration' => $video->duration,
            'channelName' => $channelName,
            'channelInitial' => $channelInitial,
            'channelColor' => $channelColor,
            'views' => $video->views,
            'publishedAt' => $video->published_at ? $video->published_at->toISOString() : now()->toISOString(),
            'category' => $video->category,
        ];
    }

    /**
     * ニュース動画データを配列形式に変換
     */
    public function transformNewsToArray(Video $video): array
    {
        $data = $this->transformToArray($video);
        $data['channelBadge'] = $data['channelName'];
        
        return $data;
    }
} 