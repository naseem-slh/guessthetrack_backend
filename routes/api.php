<?php

use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;

Route::middleware('auth:sanctum')->group( function () {
    Route::resource('/rooms', RoomController::class);
});
Route::resource('/users', UserController::class);

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});