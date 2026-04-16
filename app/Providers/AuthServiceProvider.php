<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\GameSetting;
use App\Models\Room;
use App\Models\Round;
use App\Models\SongInfo;
use App\Models\UserAnswer;
use App\Policies\GamePolicy;
use App\Policies\GameSettingPolicy;
use App\Policies\RoomPolicy;
use App\Policies\RoundPolicy;
use App\Policies\SongInfoPolicy;
use App\Policies\UserAnswerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Game::class => GamePolicy::class,
        GameSetting::class => GameSettingPolicy::class,
        Room::class => RoomPolicy::class,
        Round::class => RoundPolicy::class,
        SongInfo::class => SongInfoPolicy::class,
        UserAnswer::class => UserAnswerPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
