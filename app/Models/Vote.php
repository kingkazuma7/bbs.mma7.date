<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'fighter_id',
        'ip_address',
        'voted_at',
    ];

    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }
}
