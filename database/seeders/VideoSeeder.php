<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\User;
use Carbon\Carbon;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ユーザーが存在しない場合は作成
        $user1 = User::firstOrCreate(
            ['email' => 'dazn@example.com'],
            [
                'name' => 'DAZN Japan',
                'channel_name' => 'DAZN Japan',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'mochitaro@example.com'],
            [
                'name' => 'もちたろう',
                'channel_name' => 'もちたろう',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'uni@example.com'],
            [
                'name' => '豆柴うに&ゴールデンレトリバーおから',
                'channel_name' => '豆柴うに&ゴールデンレトリバーおから UNI&...',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $user4 = User::firstOrCreate(
            ['email' => 'channel@example.com'],
            [
                'name' => 'チャンネル名',
                'channel_name' => 'チャンネル名',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $user5 = User::firstOrCreate(
            ['email' => 'another@example.com'],
            [
                'name' => '別のチャンネル',
                'channel_name' => '別のチャンネル',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $user6 = User::firstOrCreate(
            ['email' => 'sample@example.com'],
            [
                'name' => 'サンプルチャンネル',
                'channel_name' => 'サンプルチャンネル',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // ニュース用チャンネル
        $userTBS = User::firstOrCreate(
            ['email' => 'tbs@example.com'],
            [
                'name' => 'TBS NEWS DIG',
                'channel_name' => 'TBS NEWS DIG',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $userNTV = User::firstOrCreate(
            ['email' => 'ntv@example.com'],
            [
                'name' => '日テレNEWS',
                'channel_name' => '日テレNEWS',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $userMezamashi = User::firstOrCreate(
            ['email' => 'mezamashi@example.com'],
            [
                'name' => 'めざましテレビ',
                'channel_name' => 'めざましテレビ',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // 動画データの作成
        $videos = [
            [
                'title' => '【今夏移籍】レオ ゴメス（ジュビロ磐田→京都サンガF.C.）悲願のJ1制覇へ繋...',
                'description' => 'DAZN提供のサッカー動画です。',
                'thumbnail' => 'https://picsum.photos/320/180?random=1',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
                'duration' => '6:17',
                'user_id' => $user1->id,
                'category' => 'sports',
                'views' => 17000,
                'status' => 'published',
                'published_at' => Carbon::parse('2024-01-20T09:00:00Z'),
            ],
            [
                'title' => 'はじめてのアイス もちたろう',
                'description' => 'もちたろうの可愛い動画です。',
                'thumbnail' => 'https://picsum.photos/320/180?random=2',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                'duration' => '0:50',
                'user_id' => $user2->id,
                'category' => 'pets',
                'views' => 620000,
                'status' => 'published',
                'published_at' => Carbon::parse('2023-08-15T14:30:00Z'),
            ],
            [
                'title' => '自分のこだと思い込み母親のように接するゴールデンレトリバーが優しすぎました...',
                'description' => 'ゴールデンレトリバーの優しい動画です。',
                'thumbnail' => 'https://picsum.photos/320/180?random=3',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
                'duration' => '9:10',
                'user_id' => $user3->id,
                'category' => 'pets',
                'views' => 2360000,
                'status' => 'published',
                'published_at' => Carbon::parse('2023-05-10T16:45:00Z'),
            ],
            [
                'title' => 'サンプル動画のタイトルがここに表示されます',
                'description' => 'エンターテイメント動画のサンプルです。',
                'thumbnail' => 'https://picsum.photos/320/180?random=4',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'duration' => '12:35',
                'user_id' => $user4->id,
                'category' => 'entertainment',
                'views' => 100000,
                'status' => 'published',
                'published_at' => Carbon::parse('2024-01-18T12:00:00Z'),
            ],
            [
                'title' => '別のサンプル動画のタイトル',
                'description' => '教育関連の動画です。',
                'thumbnail' => 'https://picsum.photos/320/180?random=5',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'duration' => '8:22',
                'user_id' => $user5->id,
                'category' => 'education',
                'views' => 50000,
                'status' => 'published',
                'published_at' => Carbon::parse('2024-01-13T10:15:00Z'),
            ],
            [
                'title' => '長いタイトルの動画サンプルです。これは非常に長いタイトルの例で、複数行にわたって表示される可能性があります。',
                'description' => 'テクノロジー関連の動画です。',
                'thumbnail' => 'https://picsum.photos/320/180?random=6',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
                'duration' => '15:43',
                'user_id' => $user6->id,
                'category' => 'technology',
                'views' => 1000000,
                'status' => 'published',
                'published_at' => Carbon::parse('2024-01-17T08:30:00Z'),
            ],
            // ニュース動画
            [
                'title' => '重要なニュースのタイトルがここに表示されます',
                'description' => 'TBS NEWS DIGからの重要なニュースです。',
                'thumbnail' => 'https://picsum.photos/400/225?random=7',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
                'duration' => '3:45',
                'user_id' => $userTBS->id,
                'category' => 'news',
                'views' => 500000,
                'status' => 'published',
                'published_at' => Carbon::parse('2024-01-20T07:00:00Z'),
            ],
            [
                'title' => 'ウクライナ【クリミア大橋】の一部を爆破 除去作業公開',
                'description' => '日テレNEWSからの国際ニュースです。',
                'thumbnail' => 'https://picsum.photos/400/225?random=8',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
                'duration' => '5:22',
                'user_id' => $userNTV->id,
                'category' => 'news',
                'views' => 300000,
                'status' => 'published',
                'published_at' => Carbon::parse('2024-01-20T05:30:00Z'),
            ],
            [
                'title' => '最新の社会情勢に関するニュース',
                'description' => 'めざましテレビからの社会ニュースです。',
                'thumbnail' => 'https://picsum.photos/400/225?random=9',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4',
                'duration' => '4:18',
                'user_id' => $userMezamashi->id,
                'category' => 'news',
                'views' => 250000,
                'status' => 'published',
                'published_at' => Carbon::parse('2024-01-20T04:00:00Z'),
            ],
        ];

        foreach ($videos as $videoData) {
            Video::updateOrCreate(
                [
                    'title' => $videoData['title'],
                    'user_id' => $videoData['user_id']
                ],
                $videoData
            );
        }
    }
}
