<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentDislike extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_comment_dislikes';

    protected $fillable = [
        'comment_id',
        'user_id'
    ];

    /**
     * コメントとの関連
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'id');
    }

    /**
     * ユーザーとの関連
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
