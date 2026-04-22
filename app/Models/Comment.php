<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasIpAddress;
use App\Models\Fighter;

class Comment extends Model
{
    use HasFactory;
    use HasIpAddress;
    
    protected $fillable = ['fighter_id', 'content', 'ip_address', 'user_agent', 'vote_type', 'good_count', 'bad_count'];

    protected $appends = ['vote_label'];

    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }

    public function getVoteLabel(): string
    {
        return match ($this->vote_type) {
            'strong' => '好き派',
            'weak' => '嫌い派',
            default => '匿名',
        };
    }

    public function getVoteLabelAttribute(): string
    {
        return $this->getVoteLabel();
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->content = htmlspecialchars($model->content, ENT_QUOTES, 'UTF-8');
        });
    }
}
