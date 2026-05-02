<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVoteNotification extends Notification
{
    use Queueable;

    protected $vote;

    /**
     * Create a new notification instance.
     */
    public function __construct($vote)
    {
        $this->vote = $vote;
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
        $fighterName = $this->vote->fighter->name ?? '不明な格闘家';
        $voteTypeLabel = $this->vote->vote_type === 'strong' ? '強い' : '弱い';

        return (new MailMessage)
                    ->subject('【ガチ格】新しい投票がありました：' . $fighterName)
                    ->greeting('管理者様')
                    ->line($fighterName . ' に新しい投票がありました。')
                    ->line('投票内容: ' . $voteTypeLabel)
                    ->line('IPアドレス: ' . $this->vote->ip_address)
                    ->action('サイトを確認する', url('/?id=' . $this->vote->fighter_id))
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
