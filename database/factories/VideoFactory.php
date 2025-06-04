<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Video;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['entertainment', 'music', 'sports', 'news', 'education', 'technology', 'pets'];
        
        return [
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(3),
            'thumbnail' => 'https://via.placeholder.com/320x180/FF6B6B/FFFFFF?text=' . urlencode($this->faker->word),
            'duration' => $this->faker->randomElement(['1:30', '2:45', '5:12', '8:33', '12:15', '15:45']),
            'user_id' => User::factory(),
            'category' => $this->faker->randomElement($categories),
            'views' => $this->faker->numberBetween(100, 1000000),
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * 公開済み動画の状態
     */
    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'published',
                'published_at' => now(),
            ];
        });
    }

    /**
     * 下書き動画の状態
     */
    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'published_at' => null,
            ];
        });
    }

    /**
     * 非公開動画の状態
     */
    public function private()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'private',
                'published_at' => null,
            ];
        });
    }

    /**
     * ニュース動画の状態
     */
    public function news()
    {
        return $this->state(function (array $attributes) {
            return [
                'category' => 'news',
                'title' => $this->faker->sentence(4) . ' ニュース',
            ];
        });
    }

    /**
     * スポーツ動画の状態
     */
    public function sports()
    {
        return $this->state(function (array $attributes) {
            return [
                'category' => 'sports',
                'title' => $this->faker->sentence(4) . ' スポーツ',
            ];
        });
    }

    /**
     * 音楽動画の状態
     */
    public function music()
    {
        return $this->state(function (array $attributes) {
            return [
                'category' => 'music',
                'title' => $this->faker->sentence(4) . ' 音楽',
            ];
        });
    }
}
