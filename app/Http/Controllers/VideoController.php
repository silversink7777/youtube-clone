<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoUploadRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Models\Video;
use App\Repositories\VideoRepository;
use App\Services\VideoUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response as HttpResponse;

class VideoController extends Controller
{
    private VideoRepository $videoRepository;
    private VideoUploadService $uploadService;

    public function __construct(VideoRepository $videoRepository, VideoUploadService $uploadService)
    {
        $this->videoRepository = $videoRepository;
        $this->uploadService = $uploadService;
    }

    /**
     * 動画一覧を取得
     */
    public function index(): Response
    {
        $videos = $this->videoRepository->getPublishedVideos();
        
        // データを配列形式に変換
        $videosArray = $videos->map(function ($video) {
            return $this->videoRepository->transformToArray($video);
        });
        
        return Inertia::render('YouTube', [
            'videos' => $videosArray
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
    public function show(Video $video): Response
    {
        $videoModel = $this->videoRepository->getVideoWithDetails($video->id);
        
        if (!$videoModel) {
            abort(404, 'Video not found');
        }
        
        // データを配列形式に変換
        $videoData = $this->videoRepository->transformToArray($videoModel);
        
        return Inertia::render('Watch/Show', [
            'video' => $videoData
        ]);
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

    /**
     * 動画アップロードページを表示
     */
    public function create(): Response
    {
        return Inertia::render('Upload/Create');
    }

    /**
     * 動画をアップロード
     */
    public function store(VideoUploadRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $video = $this->uploadService->uploadVideo($request, $user);
            
            return redirect()->route('videos.manage')
                ->with('success', '動画が正常にアップロードされました。');
        } catch (\Exception $e) {
            Log::error('Video upload error: ' . $e->getMessage());
            return back()->withErrors(['upload' => '動画のアップロードに失敗しました。']);
        }
    }

    /**
     * ユーザーの動画管理ページを表示
     */
    public function manage(): Response
    {
        $videos = $this->uploadService->getUserVideos(Auth::user());
        $stats = $this->uploadService->getUserVideoStats(Auth::user());
        
        return Inertia::render('Upload/Manage', [
            'videos' => $videos,
            'stats' => $stats
        ]);
    }

    /**
     * 動画編集ページを表示
     */
    public function edit(Video $video): Response
    {
        // 動画の所有者チェック
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Upload/Edit', [
            'video' => $video
        ]);
    }

    /**
     * 動画情報を更新
     */
    public function update(UpdateVideoRequest $request, Video $video): RedirectResponse
    {
        // 動画の所有者チェック
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->uploadService->updateVideo($video, $request);
            
            return redirect()->route('videos.manage')
                ->with('success', '動画情報が更新されました。');
        } catch (\Exception $e) {
            Log::error('Video update error: ' . $e->getMessage());
            return back()->withErrors(['update' => '動画の更新に失敗しました。']);
        }
    }

    /**
     * 動画を削除
     */
    public function destroy(Video $video): RedirectResponse
    {
        // 動画の所有者チェック
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->uploadService->deleteVideo($video);
            
            return redirect()->route('videos.manage')
                ->with('success', '動画が削除されました。');
        } catch (\Exception $e) {
            Log::error('Video delete error: ' . $e->getMessage());
            return back()->withErrors(['delete' => '動画の削除に失敗しました。']);
        }
    }

    /**
     * アップロード進捗を取得
     */
    public function getUploadProgress(): array
    {
        $userId = Auth::id();
        $progress = cache()->get("video_upload_progress_{$userId}", 0);
        
        return ['progress' => $progress];
    }

    /**
     * 外部動画URLのプロキシ機能
     */
    public function proxy(Request $request)
    {
        $url = $request->get('url');
        
        // URLの検証
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            abort(400, 'Invalid URL');
        }

        // 許可されたドメインの検証（セキュリティ向上のため）
        $allowedDomains = [
            'commondatastorage.googleapis.com',
            'storage.googleapis.com',
            'youtube.com',
            'googlevideo.com',
            'ytimg.com'
        ];

        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';
        
        $isAllowed = false;
        foreach ($allowedDomains as $allowedDomain) {
            if (str_ends_with($domain, $allowedDomain)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            abort(403, 'Domain not allowed');
        }

        try {
            // 外部URLからファイルを取得
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                abort(404, 'Video not found');
            }

            // レスポンスヘッダーを設定
            $headers = [
                'Content-Type' => $response->header('Content-Type') ?: 'video/mp4',
                'Content-Length' => $response->header('Content-Length'),
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=3600',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Range, Content-Type',
            ];

            // Rangeリクエストの処理
            if ($request->hasHeader('Range')) {
                $range = $request->header('Range');
                $headers['Content-Range'] = $response->header('Content-Range');
                
                return response($response->body(), 206, $headers);
            }

            return response($response->body(), 200, $headers);
        } catch (\Exception $e) {
            Log::error('Video proxy error: ' . $e->getMessage());
            abort(500, 'Failed to fetch video');
        }
    }
}
