<?php

namespace Tests\Feature;

use App\Models\Fighter;
use App\Models\Vote;
use App\Notifications\NewVoteNotification;
use App\Services\VoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VoteNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent_when_user_votes()
    {
        Notification::fake();

        // テスト用の格闘家を作成
        $fighter = Fighter::factory()->create(['name' => 'テスト格闘家']);

        // VoteService を使って投票
        $service = app(VoteService::class);
        $service->vote($fighter, 'strong', '123.123.123.123'); // 一般ユーザーのIP

        // 特定のメールアドレスに送信されたか確認
        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            NewVoteNotification::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === 'ps3neito@yahoo.co.jp';
            }
        );
    }

    public function test_notification_is_not_sent_when_admin_votes()
    {
        Notification::fake();

        // 管理者IPを設定
        $adminIp = '1.1.1.1';
        config(['app.admin_ip' => $adminIp]);

        $fighter = Fighter::factory()->create();

        // VoteService を使って投票（管理者IP）
        $service = app(VoteService::class);
        $service->vote($fighter, 'strong', $adminIp);

        // 通知が送信されていないことを確認
        Notification::assertNothingSent();
    }
}
