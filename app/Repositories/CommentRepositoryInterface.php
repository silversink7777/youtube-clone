<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Video;
use Illuminate\Pagination\LengthAwarePaginator;

interface CommentRepositoryInterface
{
    /**
     * 動画のコメント一覧を取得
     */
    public function getCommentsByVideo(Video $video, int $perPage = 20): LengthAwarePaginator;

    /**
     * コメントを作成
     */
    public function createComment(array $data): Comment;

    /**
     * コメントを削除
     */
    public function deleteComment(Comment $comment): bool;

    /**
     * コメントのピン留め状態を切り替え
     */
    public function togglePin(Comment $comment): Comment;

    /**
     * コメントのいいね状態を切り替え
     */
    public function toggleLike(Comment $comment, int $userId): array;

    /**
     * コメントのきらい状態を切り替え
     */
    public function toggleDislike(Comment $comment, int $userId): array;

    /**
     * コメントをフォーマット
     */
    public function formatComment(Comment $comment): array;

    /**
     * 特定の動画とコメントIDでコメントを取得
     */
    public function findCommentByVideoAndId(int $videoId, int $commentId): Comment;
} 