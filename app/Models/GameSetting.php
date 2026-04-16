<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSetting extends Model
{
    protected $fillable = ['room_id', 'rounds_count', 'genre'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
