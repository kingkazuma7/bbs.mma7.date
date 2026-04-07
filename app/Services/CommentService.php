<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Cache;

class CommentService
{
    /**
     * コメントを投稿
     */
    public function postComment(int $fighterId, string $content, string $ipAddress): Comment
    {
        $comment = Comment::create([
            'fighter_id' => $fighterId,
            'content' => $content,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
        ]);

        return $comment;
    }

    /**
     * 重複コメント投稿検知（オプション）
     */
    public function canPostComment(string $ipAddress, int $minutes = 1): bool
    {
        $cacheKey = 'comment_post_ip_' . $ipAddress;
        if (Cache::has($cacheKey)) {
            return false;
        }

        Cache::put($cacheKey, true, now()->addMinutes($minutes));
        return true;
    }

    /**
     * コメントコンテンツのバリデーション（オプション）
     */
    public function validateContent(string $content): bool
    {
        // 長さチェック
        if (mb_strlen($content) < 3 || mb_strlen($content) > 500) {
            return false;
        }

        // スパムキーワード検知（例）
        $spamKeywords = ['spam', 'bot', 'sex', 'viagra']; // design_doc.md を参考に具体的なキーワードを定義
        foreach ($spamKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                return false;
            }
        }

        return true;
    }
}
