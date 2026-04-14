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
    
    protected $fillable = ['fighter_id', 'user_name', 'content', 'ip_address', 'user_agent'];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->content = htmlspecialchars($model->content, ENT_QUOTES, 'UTF-8');
        });
    }
}
