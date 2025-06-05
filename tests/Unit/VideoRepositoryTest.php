<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\VideoRepository;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use App\Models\Video;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $videoRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->videoRepository = app(VideoRepositoryInterface::class);
    }

    /** @test */
    public function it_can_get_published_videos()
    {
        // Arrange
        $user = User::factory()->create();
        $publishedVideo = Video::factory()->create([
            'user_id' => $user->id,
            'status' => 'published',
            'published_at' => now(),
        ]);
        $draftVideo = Video::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        // Act
        $videos = $this->videoRepository->getPublishedVideos();

        // Assert
        $this->assertCount(1, $videos);
        $this->assertEquals($publishedVideo->id, $videos->first()->id);
    }

    /** @test */
    public function it_can_get_news_videos()
    {
        // Arrange
        $user = User::factory()->create();
        $newsVideo = Video::factory()->create([
            'user_id' => $user->id,
            'category' => 'news',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $sportsVideo = Video::factory()->create([
            'user_id' => $user->id,
            'category' => 'sports',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Act
        $newsVideos = $this->videoRepository->getNewsVideos();

        // Assert
        $this->assertCount(1, $newsVideos);
        $this->assertEquals($newsVideo->id, $newsVideos->first()->id);
    }

    /** @test */
    public function it_can_transform_video_to_array()
    {
        // Arrange
        $user = User::factory()->create(['name' => 'Test User']);
        $video = Video::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Video',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Act
        $result = $this->videoRepository->transformToArray($video);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($video->id, $result['id']);
        $this->assertEquals($video->title, $result['title']);
        $this->assertEquals('Test User', $result['channelName']);
        $this->assertEquals('T', $result['channelInitial']);
        $this->assertArrayHasKey('channelColor', $result);
    }

    /** @test */
    public function it_can_filter_videos_by_category()
    {
        // Arrange
        $user = User::factory()->create();
        $sportsVideo = Video::factory()->create([
            'user_id' => $user->id,
            'category' => 'sports',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $musicVideo = Video::factory()->create([
            'user_id' => $user->id,
            'category' => 'music',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Act
        $sportsVideos = $this->videoRepository->getPublishedVideos('sports');

        // Assert
        $this->assertCount(1, $sportsVideos);
        $this->assertEquals($sportsVideo->id, $sportsVideos->first()->id);
    }

    /** @test */
    public function it_can_search_videos()
    {
        // Arrange
        $user1 = User::factory()->create(['name' => 'Different User']);
        $user2 = User::factory()->create(['name' => 'Another User']);
        
        $matchingVideo = Video::factory()->create([
            'user_id' => $user1->id,
            'title' => 'Unique Search Test Video',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $nonMatchingVideo = Video::factory()->create([
            'user_id' => $user2->id,
            'title' => 'Different Video',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Act
        $searchResults = $this->videoRepository->getPublishedVideos(null, 'Unique');

        // Assert
        $this->assertCount(1, $searchResults);
        $this->assertEquals($matchingVideo->id, $searchResults->first()->id);
    }
}
