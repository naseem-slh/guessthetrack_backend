<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Round extends Model
{
    protected $fillable = ['game_id', 'round_info_id'];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function roundInfo(): BelongsTo
    {
        return $this->belongsTo(RoundInfo::class);
    }

    public function userScores(): HasMany
    {
        return $this->hasMany(UserScore::class);
    }

    public function calculateUserScore(): void
    {
        foreach ($this->roundInfo->userAnswers as $userAnswer) {
            $score = $this->roundInfo->evalAnswer($userAnswer);
            UserScore::updateOrCreate(
                ['user_id' => $userAnswer->user_id, 'round_id' => $this->id],
                ['score' => $score]
            );
        }
    }
}
