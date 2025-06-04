<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Video;
use App\Models\CommentLike;
use App\Models\CommentDislike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * 動画のコメント一覧を取得
     */
    public function index(Request $request, $videoId)
    {
        try {
            \Log::info('CommentController::index開始', ['video_id' => $videoId]);
            
            $video = Video::findOrFail($videoId);
            \Log::info('Video取得成功', ['video_id' => $video->id, 'title' => $video->title]);
            
            // 削除されたコメントも含めて取得（withTrashed()を使用）
            $comments = $video->topLevelComments()
                ->withTrashed()
                ->with(['user', 'replies' => function($query) {
                    $query->withTrashed()->with('user');
                }])
                ->paginate(20);
            \Log::info('topLevelComments取得成功', ['comments_count' => $comments->total()]);
            
            $formattedComments = collect($comments->items())->map(function ($comment) {
                return $this->formatComment($comment);
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

            $comment = Comment::create([
                'video_id' => $video->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'parent_id' => $request->parent_id,
            ]);

            // 作成されたコメントをリレーションと一緒に取得
            $comment->load('user', 'replies.user');

            \Log::info('コメントが正常に投稿されました', [
                'comment_id' => $comment->id,
                'video_id' => $video->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'comment' => $this->formatComment($comment),
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

        $comment = Comment::where('video_id', $videoId)
            ->where('id', $commentId)
            ->firstOrFail();

        // コメント投稿者か動画投稿者のみ削除可能
        $video = Video::findOrFail($videoId);
        if ($comment->user_id !== Auth::id() && $video->user_id !== Auth::id()) {
            return response()->json(['error' => '削除権限がありません'], 403);
        }

        $comment->delete();

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

        $comment->update(['is_pinned' => !$comment->is_pinned]);

        return response()->json([
            'comment' => $this->formatComment($comment->fresh('user')),
            'message' => $comment->is_pinned ? 'コメントをピン留めしました' : 'ピン留めを解除しました'
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
            $comment = Comment::where('video_id', $videoId)
                ->where('id', $commentId)
                ->firstOrFail();

            $userId = Auth::id();
            $existingLike = CommentLike::where('comment_id', $commentId)
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {
                // いいね解除
                $existingLike->delete();
                $action = 'unliked';
                $message = 'いいねを解除しました';
            } else {
                // いいねする前に、既存のきらいを削除
                CommentDislike::where('comment_id', $commentId)
                    ->where('user_id', $userId)
                    ->delete();

                // いいね追加
                CommentLike::create([
                    'comment_id' => $commentId,
                    'user_id' => $userId
                ]);
                $action = 'liked';
                $message = 'いいねしました';
            }

            // 最新のいいね数ときらい数を取得
            $likesCount = CommentLike::where('comment_id', $commentId)->count();
            $dislikesCount = CommentDislike::where('comment_id', $commentId)->count();

            return response()->json([
                'action' => $action,
                'message' => $message,
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount,
                'is_liked' => $action === 'liked',
                'is_disliked' => false
            ], 200, [], JSON_UNESCAPED_UNICODE);

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
            $comment = Comment::where('video_id', $videoId)
                ->where('id', $commentId)
                ->firstOrFail();

            $userId = Auth::id();
            $existingDislike = CommentDislike::where('comment_id', $commentId)
                ->where('user_id', $userId)
                ->first();

            if ($existingDislike) {
                // きらい解除
                $existingDislike->delete();
                $action = 'undisliked';
                $message = 'きらいを解除しました';
            } else {
                // きらいする前に、既存のいいねを削除
                CommentLike::where('comment_id', $commentId)
                    ->where('user_id', $userId)
                    ->delete();

                // きらい追加
                CommentDislike::create([
                    'comment_id' => $commentId,
                    'user_id' => $userId
                ]);
                $action = 'disliked';
                $message = 'きらいしました';
            }

            // 最新のいいね数ときらい数を取得
            $likesCount = CommentLike::where('comment_id', $commentId)->count();
            $dislikesCount = CommentDislike::where('comment_id', $commentId)->count();

            return response()->json([
                'action' => $action,
                'message' => $message,
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount,
                'is_liked' => false,
                'is_disliked' => $action === 'disliked'
            ], 200, [], JSON_UNESCAPED_UNICODE);

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

    /**
     * コメントデータをフォーマット
     */
    private function formatComment(Comment $comment): array
    {
        // 削除されたコメントの場合
        if ($comment->trashed()) {
            return [
                'id' => $comment->id,
                'content' => 'このコメントは削除されました',
                'likes_count' => 0,
                'dislikes_count' => 0,
                'is_pinned' => false,
                'is_deleted' => true,
                'is_liked' => false,
                'is_disliked' => false,
                'time_ago' => $comment->time_ago,
                'created_at' => $comment->created_at->toISOString(),
                'user' => [
                    'id' => null,
                    'name' => '削除されたユーザー',
                    'channel_name' => '削除されたユーザー',
                    'channel_initial' => '削',
                ],
                'replies' => $comment->replies->map(function ($reply) {
                    return $this->formatComment($reply);
                })->toArray(),
                'parent_id' => $comment->parent_id,
                'can_delete' => false,
            ];
        }

        // いいね数の取得
        $likesCount = CommentLike::where('comment_id', $comment->id)->count();
        $isLiked = Auth::check() ? CommentLike::where('comment_id', $comment->id)
            ->where('user_id', Auth::id())
            ->exists() : false;

        // きらい数の取得
        $dislikesCount = CommentDislike::where('comment_id', $comment->id)->count();
        $isDisliked = Auth::check() ? CommentDislike::where('comment_id', $comment->id)
            ->where('user_id', Auth::id())
            ->exists() : false;

        // 通常のコメント
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
            'is_pinned' => $comment->is_pinned,
            'is_deleted' => false,
            'is_liked' => $isLiked,
            'is_disliked' => $isDisliked,
            'time_ago' => $comment->time_ago,
            'created_at' => $comment->created_at->toISOString(),
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'channel_name' => $comment->user->channel_name ?? $comment->user->name,
                'channel_initial' => $comment->user->channel_initial,
            ],
            'replies' => $comment->replies->map(function ($reply) {
                return $this->formatComment($reply);
            })->toArray(),
            'parent_id' => $comment->parent_id,
            'can_delete' => Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->videos()->where('id', $comment->video_id)->exists()),
        ];
    }
}
