<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use App\Models\Video;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class VideoUploadService
{
    private $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * 動画をアップロードしてデータベースに保存
     */
    public function uploadVideo(array $data, UploadedFile $videoFile, ?UploadedFile $thumbnailFile = null): Video
    {
        try {
            // 動画ファイルを保存
            $videoPath = $this->storeVideoFile($videoFile);
            
            // サムネイル処理
            $thumbnailPath = $thumbnailFile 
                ? $this->storeThumbnailFile($thumbnailFile)
                : $this->generateDefaultThumbnail();

            // 動画メタデータを取得
            $duration = $this->getVideoDuration($videoPath);

            // データベースに保存
            $video = Video::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'video_url' => $videoPath,
                'thumbnail' => $thumbnailPath,
                'duration' => $duration,
                'user_id' => auth()->id(),
                'category' => $data['category'],
                'status' => $data['status'],
                'views' => 0,
                'published_at' => $data['status'] === 'published' ? now() : null,
            ]);

            Log::info('Video uploaded successfully', [
                'video_id' => $video->id,
                'user_id' => auth()->id(),
                'title' => $data['title']
            ]);

            return $video;

        } catch (Exception $e) {
            Log::error('Video upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * 動画ファイルを保存
     */
    private function storeVideoFile(UploadedFile $file): string
    {
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('videos', $fileName, 'public');
        
        return $path;
    }

    /**
     * サムネイルファイルを保存
     */
    public function storeThumbnailFile(UploadedFile $file): string
    {
        $fileName = Str::uuid() . '.jpg';
        
        try {
            // 画像をリサイズして保存
            $image = $this->imageManager->read($file->getRealPath())
                ->resize(1280, 720);

            $path = 'thumbnails/' . $fileName;
            $encodedImage = $image->toJpeg(85);
            Storage::disk('public')->put($path, $encodedImage);
            
            return $path;
        } catch (Exception $e) {
            Log::error('Failed to process thumbnail', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            
            // フォールバック: オリジナルファイルをそのまま保存
            $path = $file->storeAs('thumbnails', $fileName, 'public');
            return $path;
        }
    }

    /**
     * デフォルトサムネイルを生成
     */
    private function generateDefaultThumbnail(): string
    {
        try {
            // デフォルトのサムネイル画像を作成
            $image = $this->imageManager->create(1280, 720)->fill('#2d3748');
            
            $fileName = Str::uuid() . '.jpg';
            $path = 'thumbnails/' . $fileName;
            
            $encodedImage = $image->toJpeg(85);
            Storage::disk('public')->put($path, $encodedImage);
            
            return $path;
        } catch (Exception $e) {
            Log::error('Failed to generate default thumbnail', [
                'error' => $e->getMessage()
            ]);
            
            // フォールバック: 簡単な色付き画像のみ作成
            $image = $this->imageManager->create(1280, 720)->fill('#2d3748');
            $fileName = Str::uuid() . '.jpg';
            $path = 'thumbnails/' . $fileName;
            $encodedImage = $image->toJpeg(85);
            Storage::disk('public')->put($path, $encodedImage);
            
            return $path;
        }
    }

    /**
     * 動画の再生時間を取得（簡易実装）
     * 実際のプロダクションではFFMpegなどを使用して実際の動画時間を取得
     */
    private function getVideoDuration(string $videoPath): ?string
    {
        // 簡易実装：デフォルト値を返す
        // 実際の実装では getid3 や ffmpeg を使用して実際の動画時間を取得
        return '00:00';
    }

    /**
     * アップロード進捗を更新（WebSocket実装時に使用）
     */
    public function updateProgress(string $sessionId, int $progress): void
    {
        // 進捗情報をキャッシュに保存
        cache()->put("upload_progress_{$sessionId}", $progress, 300); // 5分間保持
    }

    /**
     * アップロード進捗を取得
     */
    public function getProgress(string $sessionId): int
    {
        return cache()->get("upload_progress_{$sessionId}", 0);
    }

    /**
     * ユーザーの動画を取得
     */
    public function getUserVideos(User $user, int $perPage = 12)
    {
        return $user->videos()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 動画を削除
     */
    public function deleteVideo(Video $video): bool
    {
        try {
            // ファイルを削除
            if ($video->video_url) {
                Storage::disk('public')->delete($video->video_url);
            }
            
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }

            // データベースから削除
            $video->delete();

            Log::info('Video deleted successfully', [
                'video_id' => $video->id,
                'user_id' => $video->user_id
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Video deletion failed', [
                'video_id' => $video->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
} 