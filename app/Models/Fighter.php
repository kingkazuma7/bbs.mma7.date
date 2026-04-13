<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Vote;
use App\Models\Comment;

class Fighter extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image_url'];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // 新規追加: 投票数を取得するアクセサ
    public function getVotesCountAttribute()
    {
        return $this->votes()->count();
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
