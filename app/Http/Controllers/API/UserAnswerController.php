<?php

namespace App\Http\Controllers\API;

use App\Models\UserAnswer;
use Illuminate\Http\Request;

class UserAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserAnswer::with(['user', 'roundInfo', 'songInfo'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'round_info_id' => 'required|exists:round_infos,id',
            'song_info_id' => 'required|exists:song_infos,id',
        ]);

        return UserAnswer::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(UserAnswer $userAnswer)
    {
        return $userAnswer->load(['user', 'roundInfo', 'songInfo']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserAnswer $userAnswer)
    {
        $request->validate([
            'song_info_id' => 'sometimes|exists:song_infos,id',
        ]);

        $userAnswer->update($request->all());
        return $userAnswer;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserAnswer $userAnswer)
    {
        $userAnswer->delete();
        return response()->noContent();
    }
}
