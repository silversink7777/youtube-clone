<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Video extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_videos';

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'duration',
        'user_id',
        'category',
        'views',
        'status',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer'
    ];

    protected $appends = [
        'channel_name',
        'channel_initial',
        'channel_color'
    ];

    /**
     * ユーザーとの関連
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * チャンネル名を取得
     */
    public function getChannelNameAttribute(): string
    {
        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->channel_name ?? $this->user->name;
        }
        return 'Unknown Channel';
    }

    /**
     * チャンネルの頭文字を取得
     */
    public function getChannelInitialAttribute(): string
    {
        $name = $this->channel_name;
        return mb_substr($name, 0, 1);
    }

    /**
     * チャンネルカラーを取得（ランダム）
     */
    public function getChannelColorAttribute(): string
    {
        $colors = [
            'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 
            'bg-purple-500', 'bg-pink-500', 'bg-indigo-500',
            'bg-red-500', 'bg-orange-500', 'bg-teal-500'
        ];
        
        return $colors[$this->user_id % count($colors)];
    }

    /**
     * 公開された動画のみを取得するスコープ
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * カテゴリ別の動画を取得するスコープ
     */
    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * 検索スコープ
     */
    public function scopeSearch($query, $searchTerm)
    {
        if ($searchTerm) {
            return $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', '%' . $searchTerm . '%')
                               ->orWhere('channel_name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        return $query;
    }
}
