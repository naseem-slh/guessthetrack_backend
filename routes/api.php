<?php

use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\GameSettingController;
use App\Http\Controllers\API\GameController;
use App\Http\Controllers\API\RoundController;
use App\Http\Controllers\API\SongInfoController;
use App\Http\Controllers\API\RoundInfoController;
use App\Http\Controllers\API\UserAnswerController;
use App\Http\Controllers\SpotifyController;

Route::middleware('auth:sanctum')->group( function () {
    Route::resource('/rooms', RoomController::class);
    Route::post('/rooms/{room}/invite', [RoomController::class, 'inviteUser']);
    Route::post('/rooms/{room}/accept-invitation', [RoomController::class, 'acceptInvitation']);
    Route::post('/rooms/{room}/decline-invitation', [RoomController::class, 'declineInvitation']);
    Route::delete('/rooms/{room}/remove-user', [RoomController::class, 'removeUser']);
    Route::resource('/games', GameController::class);
    Route::get('/games/{game}/total-scores', [GameController::class, 'totalScores']);
    Route::resource('/rounds', RoundController::class);
    Route::post('/rounds/{round}/calculate-scores', [RoundController::class, 'calculateScores']);
    Route::resource('/user-answers', UserAnswerController::class);
    Route::get('/spotify/login-url', [SpotifyController::class, 'loginUrl'])->name('spotify.login-url');
    Route::resource('/users', UserController::class);
    Route::resource('/game-settings', GameSettingController::class);
    Route::resource('/song-infos', SongInfoController::class);
    Route::resource('/round-infos', RoundInfoController::class);
    Route::post('/round-infos/{round_info}/eval-answer', [RoundInfoController::class, 'evalAnswer']);
});

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});