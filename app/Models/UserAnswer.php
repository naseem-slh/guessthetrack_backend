<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    protected $fillable = ['user_id', 'round_info_id', 'song_info_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roundInfo(): BelongsTo
    {
        return $this->belongsTo(RoundInfo::class);
    }

    public function songInfo(): BelongsTo
    {
        return $this->belongsTo(SongInfo::class);
    }
}
