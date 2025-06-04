<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'channel_name',
        'channel_description',
        'subscriber_count',
        'total_views',
        'video_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'channel_initial',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscriber_count' => 'integer',
            'total_views' => 'integer',
            'video_count' => 'integer',
        ];
    }

    /**
     * Get the user's channel name or fallback to their name.
     */
    public function getChannelNameAttribute($value)
    {
        return $value ?: $this->name;
    }

    /**
     * Get the first character of the user's name for the avatar.
     */
    public function getChannelInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Format the subscriber count in Japanese style.
     */
    public function getFormattedSubscriberCountAttribute()
    {
        if ($this->subscriber_count >= 10000) {
            return number_format($this->subscriber_count / 10000, 1) . '万';
        }
        return number_format($this->subscriber_count);
    }

    /**
     * Format the total views in Japanese style.
     */
    public function getFormattedTotalViewsAttribute()
    {
        if ($this->total_views >= 10000) {
            return number_format($this->total_views / 10000, 1) . '万';
        }
        return number_format($this->total_views);
    }

    /**
     * Get videos owned by this user/channel.
     */
    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    /**
     * Get published videos only.
     */
    public function publishedVideos()
    {
        return $this->videos()->where('status', 'published');
    }

    /**
     * Get comments posted by this user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
