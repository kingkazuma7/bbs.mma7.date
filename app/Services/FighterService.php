<?php

namespace App\Services;

use App\Models\Fighter;
use Illuminate\Database\Eloquent\Collection;

class FighterService
{
    /**
     * ランダムに N 人の格闘家を取得
     */
    public function getRandomFighters(int $count = 2): Collection
    {
        return Fighter::inRandomOrder()->limit($count)->get();
    }
    
    /**
     * 全格闘家と投票統計を取得
     */
    public function getFightersWithStats(): Collection
    {
        return Fighter::with('votes')
            ->orderBy('name')
            ->get()
            ->map(function ($fighter) {
                $fighter->stats = $fighter->getVoteStats();
                return $fighter;
            });
    }
}
