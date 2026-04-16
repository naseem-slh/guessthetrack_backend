<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RoundInfo extends Model
{
    protected $fillable = ['correct_song_info_id'];

    public function correctSongInfo(): BelongsTo
    {
        return $this->belongsTo(SongInfo::class, 'correct_song_info_id');
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function round(): HasOne
    {
        return $this->hasOne(Round::class);
    }

    public function evalAnswer(UserAnswer $userAnswer): int
    {
        $correct = $this->correctSongInfo;
        $answer = $userAnswer->songInfo;

        $score = 0;
        if (strtolower($correct->title) === strtolower($answer->title)) {
            $score += 1;
        }
        if (strtolower($correct->singer) === strtolower($answer->singer)) {
            $score += 1;
        }
        if ($correct->year == $answer->year) {
            $score += 1;
        }
        return $score;
    }
}
