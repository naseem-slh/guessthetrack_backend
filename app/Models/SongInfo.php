<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongInfo extends Model
{
    protected $fillable = [
        'title',
        'singer',
        'year',
        'spotify_track_id',
        'spotify_external_urls',
        'spotify_preview_url',
        'spotify_images',
        'spotify_duration_ms',
        'spotify_uri'
    ];

    protected $casts = [
        'spotify_external_urls' => 'array',
        'spotify_images' => 'array',
    ];

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function roundInfos(): HasMany
    {
        return $this->hasMany(RoundInfo::class, 'correct_song_info_id');
    }
}
