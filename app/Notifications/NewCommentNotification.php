<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $fighterName = $this->comment->fighter->name ?? '不明な格闘家';
        $voteTypeLabel = $this->comment->vote_type === 'strong' ? '強い' : ($this->comment->vote_type === 'weak' ? '弱い' : 'なし');

        return (new MailMessage)
            ->subject('【ガチ格】新しいコメント：'.$fighterName)
            ->greeting('管理者様')
            ->line($fighterName.' に新しいコメントが投稿されました。')
            ->line('投票: '.$voteTypeLabel)
            ->line('内容: '.$this->comment->content)
            ->line('IPアドレス: '.$this->comment->ip_address)
            ->action(
                'コメントを確認する',
                $this->comment->fighter
                    ? route('people.vote', ['fighterName' => $this->comment->fighter->name]).'?show_bbs=1'
                    : url('/')
            )
            ->line('引き続きサイトの運営をお願いします！');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
