<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Video;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videos = Video::all();
        $users = User::all();

        if ($videos->isEmpty() || $users->isEmpty()) {
            return; // 動画またはユーザーが存在しない場合は実行しない
        }

        $sampleComments = [
            'とても参考になりました！ありがとうございます。',
            'すごく面白い動画でした！続編も期待してます。',
            'わかりやすい説明で助かりました。',
            'この方法は知らなかったです。勉強になります。',
            '素晴らしい内容ですね。チャンネル登録させていただきました。',
            'もう少し詳しく説明してもらえるとありがたいです。',
            '初心者にもわかりやすくて良いですね。',
            'この動画のおかげで問題が解決しました！',
            '次回の動画も楽しみにしています。',
            '質問があるのですが、〇〇の部分はどうすればいいでしょうか？',
            'とても役に立ちました。友達にもシェアします。',
            'クオリティの高い動画をありがとうございます。',
            'こんな方法があったんですね。目から鱗でした。',
            'おつかれさまです！いつも動画を楽しみにしています。',
            'コメント見に来ました。皆さんの反応も面白いですね。',
        ];

        $sampleReplies = [
            'ありがとうございます！',
            'そうですね！同感です。',
            '私も同じことを思いました。',
            'そのとおりですね。',
            'なるほど、勉強になります。',
            '質問の答えは動画の〇分頃にありますよ。',
            '私も初心者なので一緒に頑張りましょう！',
            'チャンネル登録ありがとうございます！',
            '次回も楽しみにしていてください。',
            'こちらこそありがとうございます。',
        ];

        foreach ($videos as $video) {
            // 各動画に3-8個のコメントを作成
            $commentCount = rand(3, 8);
            
            for ($i = 0; $i < $commentCount; $i++) {
                $randomUser = $users->random();
                $createdAt = Carbon::now()->subDays(rand(0, 30))->subMinutes(rand(0, 1440));
                
                $comment = Comment::create([
                    'video_id' => $video->id,
                    'user_id' => $randomUser->id,
                    'content' => $sampleComments[array_rand($sampleComments)],
                    'likes_count' => rand(0, 50),
                    'is_pinned' => $i === 0 && rand(0, 4) === 0, // 最初のコメントを20%の確率でピン留め
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // 30%の確率で返信を作成
                if (rand(0, 9) < 3) {
                    $replyCount = rand(1, 3);
                    
                    for ($j = 0; $j < $replyCount; $j++) {
                        $replyUser = $users->random();
                        $replyCreatedAt = $createdAt->copy()->addMinutes(rand(5, 60));
                        
                        Comment::create([
                            'video_id' => $video->id,
                            'user_id' => $replyUser->id,
                            'content' => $sampleReplies[array_rand($sampleReplies)],
                            'parent_id' => $comment->id,
                            'likes_count' => rand(0, 20),
                            'created_at' => $replyCreatedAt,
                            'updated_at' => $replyCreatedAt,
                        ]);
                    }
                }
            }
        }

        echo "コメントデータを作成しました。\n";
    }
}
