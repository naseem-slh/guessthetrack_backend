<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = ['room_id', 'game_setting_id'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function gameSetting(): BelongsTo
    {
        return $this->belongsTo(GameSetting::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    public function userScores(): HasMany
    {
        return $this->hasManyThrough(UserScore::class, Round::class);
    }

    public function calculateTotalScores(): array
    {
        $totalScores = [];
        foreach ($this->rounds as $round) {
            foreach ($round->userScores as $userScore) {
                $userId = $userScore->user_id;
                if (!isset($totalScores[$userId])) {
                    $totalScores[$userId] = 0;
                }
                $totalScores[$userId] += $userScore->score;
            }
        }
        return $totalScores;
    }
}
