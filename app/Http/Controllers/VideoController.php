<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    protected $videoRepository;

    public function __construct(VideoRepositoryInterface $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * 動画一覧を取得
     */
    public function index(Request $request)
    {
        // 動画データを取得
        $videos = $this->videoRepository->getPublishedVideos(
            $request->get('category'),
            $request->get('search')
        );

        // ニュース動画を取得
        $newsVideos = $this->videoRepository->getNewsVideos(3);

        // データを配列形式に変換
        $videosArray = $videos->map(function ($video) {
            return $this->videoRepository->transformToArray($video);
        });

        $newsVideosArray = $newsVideos->map(function ($video) {
            return $this->videoRepository->transformNewsToArray($video);
        });

        return Inertia::render('YouTube', [
            'videos' => $videosArray,
            'newsVideos' => $newsVideosArray,
        ]);
    }

    /**
     * API用の動画一覧を取得
     */
    public function apiIndex(Request $request)
    {
        // 動画データを取得
        $videos = $this->videoRepository->getPublishedVideos(
            $request->get('category'),
            $request->get('search')
        );

        // データを配列形式に変換
        $videosArray = $videos->map(function ($video) {
            return $this->videoRepository->transformToArray($video);
        });

        return response()->json($videosArray);
    }

    /**
     * 特定の動画を取得
     */
    public function show(int $id)
    {
        $video = $this->videoRepository->findById($id);
        
        if (!$video) {
            Log::warning("Video not found: {$id}");
            abort(404, 'Video not found');
        }

        try {
            $videoData = $this->videoRepository->transformToArray($video);
            
            // コメント数を追加
            $videoData['comments_count'] = $video->comments()->count();
            
            Log::info("Video data retrieved successfully", [
                'video_id' => $id,
                'title' => $videoData['title'] ?? 'N/A',
                'user_id' => $videoData['user_id'] ?? 'N/A',
                'comments_count' => $videoData['comments_count']
            ]);

            return Inertia::render('Watch/Show', [
                'video' => $videoData
            ]);
        } catch (\Exception $e) {
            Log::error("Error preparing video data for view", [
                'video_id' => $id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Error loading video data');
        }
    }

    /**
     * カテゴリ別動画を取得
     */
    public function byCategory(string $category)
    {
        $videos = $this->videoRepository->getVideosByCategory($category);

        $videosArray = $videos->map(function ($video) {
            return $this->videoRepository->transformToArray($video);
        });

        return response()->json($videosArray);
    }

    /**
     * 動画検索
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([]);
        }

        $videos = $this->videoRepository->searchVideos($query);

        $videosArray = $videos->map(function ($video) {
            return $this->videoRepository->transformToArray($video);
        });

        return response()->json($videosArray);
    }
}
