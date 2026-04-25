<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Vote;
use App\Models\Comment;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class Fighter extends Model
{
    use HasFactory, HasSEO;
    protected $fillable = ['name', 'image_url'];
    
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function getVoteStats()
    {
        $votes = $this->votes()->get();
        $strong = $votes->where('vote_type', 'strong')->count();
        $weak = $votes->where('vote_type', 'weak')->count();
        $total = $votes->count();

        return [
            'strong_count' => $strong,
            'weak_count' => $weak,
            'total_count' => $total,
            'strong_percentage' => $total > 0 ? round(($strong / $total) * 100, 1) : 0,
            'weak_percentage' => $total > 0 ? round(($weak / $total) * 100, 1) : 0,
        ];
    }

    public function getImagePathAttribute()
    {
        if (!$this->image_url) {
            return null;
        }

        // 既に fighters/ を含んでいる場合は、そのまま使用
        if (str_starts_with($this->image_url, 'fighters/')) {
            $path = asset('storage/' . $this->image_url);
        } else {
            // fighters/ を含んでいない場合は、プレフィックスを追加
            $path = asset('storage/fighters/' . $this->image_url);
        }

        // キャッシュバスターを追加（updated_atをパラメータに）
        return $path . '?v=' . $this->updated_at->timestamp;
    }

    public function getDynamicSEOData(): SEOData
    {
        return SEOData::make([
            'title' => $this->name,
            'description' => "{$this->name}の強い・弱い投票結果。みんなの本音をチェック！",
            'image' => $this->image_path,
        ]);
    }
}
