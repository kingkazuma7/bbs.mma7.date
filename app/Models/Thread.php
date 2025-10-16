<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function fighter(): BelongsTo
    {
        return $this->belongsTo(Fighter::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
