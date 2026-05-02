<?php

namespace Tests\Feature;

use App\Models\Fighter;
use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use App\Services\CommentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent_when_user_posts_comment()
    {
        Notification::fake();

        $fighter = Fighter::factory()->create();

        // CommentService を使ってコメント投稿
        $service = app(CommentService::class);
        $service->postComment($fighter->id, 'テストコメントです', '123.123.123.123', 'strong');

        // 特定のメールアドレスに送信されたか確認
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            NewCommentNotification::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === 'ps3neito@yahoo.co.jp';
            }
        );
    }

    public function test_notification_is_not_sent_when_admin_posts_comment()
    {
        Notification::fake();

        // 管理者IPを設定
        $adminIp = '1.1.1.1';
        config(['app.admin_ip' => $adminIp]);

        $fighter = Fighter::factory()->create();

        // CommentService を使ってコメント投稿（管理者IP）
        $service = app(CommentService::class);
        $service->postComment($fighter->id, '管理者のコメントです', $adminIp, 'strong');

        // 通知が送信されていないことを確認
        Notification::assertNothingSent();
    }
}
