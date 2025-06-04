<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'tbl_comments';

    protected $fillable = [
        'video_id',
        'user_id',
        'content',
        'parent_id',
        'likes_count',
        'is_pinned'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'likes_count' => 'integer'
    ];

    /**
     * 動画との関連
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id', 'id');
    }

    /**
     * ユーザーとの関連
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 親コメントとの関連（返信機能）
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * 子コメント（返信）との関連
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user')->orderBy('created_at', 'asc');
    }

    /**
     * 表示用の子コメント（削除されていない返信のみ）
     */
    public function visibleReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user')->orderBy('created_at', 'asc');
    }

    /**
     * いいねとの関連
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class, 'comment_id', 'id');
    }

    /**
     * きらいとの関連
     */
    public function dislikes(): HasMany
    {
        return $this->hasMany(CommentDislike::class, 'comment_id', 'id');
    }

    /**
     * 特定のユーザーがいいねしているかチェック
     */
    public function isLikedBy($userId): bool
    {
        if (!$userId) return false;
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * 特定のユーザーがきらいしているかチェック
     */
    public function isDislikedBy($userId): bool
    {
        if (!$userId) return false;
        return $this->dislikes()->where('user_id', $userId)->exists();
    }

    /**
     * いいね数を取得
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * きらい数を取得
     */
    public function getDislikesCountAttribute(): int
    {
        return $this->dislikes()->count();
    }

    /**
     * トップレベルコメント（返信ではないコメント）のスコープ
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * ピン留めコメントのスコープ
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * 特定の動画のコメントを取得するスコープ
     */
    public function scopeForVideo($query, $videoId)
    {
        return $query->where('video_id', $videoId);
    }

    /**
     * コメントが返信かどうかを判定
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * 投稿からの経過時間を取得
     */
    public function getTimeAgoAttribute(): string
    {
        $now = now();
        $diffInMinutes = $this->created_at->diffInMinutes($now);
        
        // 1分未満は「たった今」
        if ($diffInMinutes < 1) {
            return 'たった今';
        }
        
        // 60分未満は分単位（整数）
        if ($diffInMinutes < 60) {
            return intval($diffInMinutes) . '分前';
        }
        
        $diffInHours = $this->created_at->diffInHours($now);
        if ($diffInHours < 24) {
            return intval($diffInHours) . '時間前';
        }
        
        $diffInDays = $this->created_at->diffInDays($now);
        if ($diffInDays < 7) {
            return intval($diffInDays) . '日前';
        }
        
        if ($diffInDays < 30) {
            $weeks = intval($diffInDays / 7);
            return $weeks . '週前';
        }
        
        $diffInMonths = $this->created_at->diffInMonths($now);
        if ($diffInMonths < 12) {
            return intval($diffInMonths) . 'か月前';
        }
        
        $diffInYears = $this->created_at->diffInYears($now);
        return intval($diffInYears) . '年前';
    }
}
