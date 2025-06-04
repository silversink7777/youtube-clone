<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentDislike;
use App\Models\Video;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CommentRepository implements CommentRepositoryInterface
{
    /**
     * 動画のコメント一覧を取得
     */
    public function getCommentsByVideo(Video $video, int $perPage = 20): LengthAwarePaginator
    {
        return $video->topLevelComments()
            ->withTrashed()
            ->with(['user', 'replies' => function($query) {
                $query->withTrashed()->with('user');
            }])
            ->paginate($perPage);
    }

    /**
     * コメントを作成
     */
    public function createComment(array $data): Comment
    {
        $comment = Comment::create($data);
        $comment->load('user', 'replies.user');
        return $comment;
    }

    /**
     * コメントを削除
     */
    public function deleteComment(Comment $comment): bool
    {
        return $comment->delete();
    }

    /**
     * コメントのピン留め状態を切り替え
     */
    public function togglePin(Comment $comment): Comment
    {
        $comment->update(['is_pinned' => !$comment->is_pinned]);
        return $comment->fresh('user');
    }

    /**
     * コメントのいいね状態を切り替え
     */
    public function toggleLike(Comment $comment, int $userId): array
    {
        $existingLike = CommentLike::where('comment_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            // いいね解除
            $existingLike->delete();
            $action = 'unliked';
            $message = 'いいねを解除しました';
        } else {
            // いいねする前に、既存のきらいを削除
            CommentDislike::where('comment_id', $comment->id)
                ->where('user_id', $userId)
                ->delete();

            // いいね追加
            CommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => $userId
            ]);
            $action = 'liked';
            $message = 'いいねしました';
        }

        // 最新のいいね数ときらい数を取得
        $likesCount = CommentLike::where('comment_id', $comment->id)->count();
        $dislikesCount = CommentDislike::where('comment_id', $comment->id)->count();

        return [
            'action' => $action,
            'message' => $message,
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
            'is_liked' => $action === 'liked',
            'is_disliked' => false
        ];
    }

    /**
     * コメントのきらい状態を切り替え
     */
    public function toggleDislike(Comment $comment, int $userId): array
    {
        $existingDislike = CommentDislike::where('comment_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingDislike) {
            // きらい解除
            $existingDislike->delete();
            $action = 'undisliked';
            $message = 'きらいを解除しました';
        } else {
            // きらいする前に、既存のいいねを削除
            CommentLike::where('comment_id', $comment->id)
                ->where('user_id', $userId)
                ->delete();

            // きらい追加
            CommentDislike::create([
                'comment_id' => $comment->id,
                'user_id' => $userId
            ]);
            $action = 'disliked';
            $message = 'きらいしました';
        }

        // 最新のいいね数ときらい数を取得
        $likesCount = CommentLike::where('comment_id', $comment->id)->count();
        $dislikesCount = CommentDislike::where('comment_id', $comment->id)->count();

        return [
            'action' => $action,
            'message' => $message,
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
            'is_liked' => false,
            'is_disliked' => $action === 'disliked'
        ];
    }

    /**
     * コメントをフォーマット
     */
    public function formatComment(Comment $comment): array
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

    /**
     * 特定の動画とコメントIDでコメントを取得
     */
    public function findCommentByVideoAndId(int $videoId, int $commentId): Comment
    {
        return Comment::where('video_id', $videoId)
            ->where('id', $commentId)
            ->firstOrFail();
    }
} 