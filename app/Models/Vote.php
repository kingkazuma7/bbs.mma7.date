<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasIpAddress;
use App\Models\Fighter;

class Vote extends Model
{
    use HasFactory;
    use HasIpAddress;
    
    protected $fillable = ['fighter_id', 'vote_type', 'ip_address', 'user_agent'];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('vote_type', $type);
    }
    
    public function scopeByFighter($query, $fighterId)
    {
        return $query->where('fighter_id', $fighterId);
    }
}
