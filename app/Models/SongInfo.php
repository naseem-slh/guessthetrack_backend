<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongInfo extends Model
{
    protected $fillable = ['title', 'singer', 'year'];

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function roundInfos(): HasMany
    {
        return $this->hasMany(RoundInfo::class, 'correct_song_info_id');
    }
}
