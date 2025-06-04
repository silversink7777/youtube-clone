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
            'thumbnail' => $video->thumbnail,
            'video_url' => $video->video_url,
            'duration' => $video->duration,
            'channelName' => $channelName,
            'channelInitial' => $channelInitial,
            'channelColor' => $channelColor,
            'views' => $video->views,
            'publishedAt' => $video->published_at->toISOString(),
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