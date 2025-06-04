<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Video;
use App\Repositories\CommentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    protected CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * 動画のコメント一覧を取得
     */
    public function index(Request $request, $videoId)
    {
        try {
            \Log::info('CommentController::index開始', ['video_id' => $videoId]);
            
            $video = Video::findOrFail($videoId);
            \Log::info('Video取得成功', ['video_id' => $video->id, 'title' => $video->title]);
            
            $comments = $this->commentRepository->getCommentsByVideo($video, 20);
            \Log::info('topLevelComments取得成功', ['comments_count' => $comments->total()]);
            
            $formattedComments = collect($comments->items())->map(function ($comment) {
                return $this->commentRepository->formatComment($comment);
            })->toArray();
            \Log::info('formatComment完了', ['formatted_count' => count($formattedComments)]);

            return response()->json([
                'comments' => $formattedComments,
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'total' => $comments->total(),
                    'per_page' => $comments->perPage(),
                ]
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('CommentController::index エラー', [
                'error' => $e->getMessage(),
                'video_id' => $videoId,
                'stack' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'コメント読み込みエラー',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * コメントを投稿
     */
    public function store(Request $request, $videoId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'ログインが必要です'], 401);
        }

        try {
            $request->validate([
                'content' => 'required|string|max:1000',
                'parent_id' => 'nullable|exists:tbl_comments,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'バリデーションエラー',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $video = Video::findOrFail($videoId);

            // 親コメントが指定されている場合、同じ動画のコメントかチェック
            if ($request->parent_id) {
                $parentComment = Comment::findOrFail($request->parent_id);
                if ($parentComment->video_id !== $video->id) {
                    return response()->json(['error' => '無効な返信です'], 422);
                }
            }

            $comment = $this->commentRepository->createComment([
                'video_id' => $video->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'parent_id' => $request->parent_id,
            ]);

            \Log::info('コメントが正常に投稿されました', [
                'comment_id' => $comment->id,
                'video_id' => $video->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'comment' => $this->commentRepository->formatComment($comment),
                'message' => 'コメントを投稿しました'
            ], 201, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('コメント投稿エラー', [
                'error' => $e->getMessage(),
                'video_id' => $videoId,
                'user_id' => Auth::id(),
                'stack' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'コメントの投稿中にエラーが発生しました',
                'message' => 'サーバーエラーが発生しました。しばらく時間をおいてから再度お試しください。'
            ], 500);
        }
    }

    /**
     * コメントを削除
     */
    public function destroy($videoId, $commentId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'ログインが必要です'], 401);
        }

        $comment = $this->commentRepository->findCommentByVideoAndId($videoId, $commentId);

        // コメント投稿者か動画投稿者のみ削除可能
        $video = Video::findOrFail($videoId);
        if ($comment->user_id !== Auth::id() && $video->user_id !== Auth::id()) {
            return response()->json(['error' => '削除権限がありません'], 403);
        }

        $this->commentRepository->deleteComment($comment);

        return response()->json(['message' => 'コメントを削除しました']);
    }

    /**
     * コメントをピン留め/解除（動画投稿者のみ）
     */
    public function togglePin($videoId, $commentId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'ログインが必要です'], 401);
        }

        $video = Video::findOrFail($videoId);
        
        // 動画投稿者のみピン留め可能
        if ($video->user_id !== Auth::id()) {
            return response()->json(['error' => 'ピン留め権限がありません'], 403);
        }

        $comment = Comment::where('video_id', $videoId)
            ->where('id', $commentId)
            ->whereNull('parent_id') // トップレベルコメントのみピン留め可能
            ->firstOrFail();

        $updatedComment = $this->commentRepository->togglePin($comment);

        return response()->json([
            'comment' => $this->commentRepository->formatComment($updatedComment),
            'message' => $updatedComment->is_pinned ? 'コメントをピン留めしました' : 'ピン留めを解除しました'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * コメントにいいね/いいね解除
     */
    public function toggleLike($videoId, $commentId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'ログインが必要です'], 401);
        }

        try {
            $comment = $this->commentRepository->findCommentByVideoAndId($videoId, $commentId);
            $result = $this->commentRepository->toggleLike($comment, Auth::id());

            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('いいね機能エラー', [
                'error' => $e->getMessage(),
                'comment_id' => $commentId,
                'user_id' => Auth::id(),
                'stack' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'いいね処理中にエラーが発生しました',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * コメントにきらい/きらい解除
     */
    public function toggleDislike($videoId, $commentId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'ログインが必要です'], 401);
        }

        try {
            $comment = $this->commentRepository->findCommentByVideoAndId($videoId, $commentId);
            $result = $this->commentRepository->toggleDislike($comment, Auth::id());

            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('きらい機能エラー', [
                'error' => $e->getMessage(),
                'comment_id' => $commentId,
                'user_id' => Auth::id(),
                'stack' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'きらい処理中にエラーが発生しました',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
