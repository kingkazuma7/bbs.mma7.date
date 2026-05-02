<?php

namespace App\Services;

use App\Models\Fighter;
use App\Models\Vote;
use App\Notifications\NewVoteNotification;
use Illuminate\Support\Facades\Notification;

class VoteService
{
    /**
     * 投票を記録
     */
    public function vote(Fighter $fighter, string $voteType, string $ipAddress): array
    {
        // 投票を記録
        $vote = Vote::create([
            'fighter_id' => $fighter->id,
            'vote_type' => $voteType,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
        ]);

        // 自分以外（管理者のIP以外）の場合に通知を送信
        // TODO: .env などで管理者のIPを指定できるようにするとより正確です
        $adminIp = config('app.admin_ip'); 
        if ($ipAddress !== $adminIp) {
            Notification::route('mail', 'ps3neito@yahoo.co.jp')
                ->notify(new NewVoteNotification($vote));
        }
        
        // 最新の統計を返す
        return $fighter->getVoteStats();
    }
    
    /**
     * 重複投票検知（オプション）
     */
    public function canVote(Fighter $fighter, string $ipAddress, int $minutes = 1): bool
    {
        $recentVote = Vote::where('fighter_id', $fighter->id)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->first();
        
        return !$recentVote;
    }
}
