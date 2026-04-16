<?php

namespace App\Http\Controllers\API;

use App\Models\RoundInfo;
use App\Models\UserAnswer;
use Illuminate\Http\Request;

class RoundInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RoundInfo::with(['correctSongInfo', 'userAnswers'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'correct_song_info_id' => 'required|exists:song_infos,id',
        ]);

        return RoundInfo::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(RoundInfo $roundInfo)
    {
        return $roundInfo->load(['correctSongInfo', 'userAnswers']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoundInfo $roundInfo)
    {
        $request->validate([
            'correct_song_info_id' => 'sometimes|exists:song_infos,id',
        ]);

        $roundInfo->update($request->all());
        return $roundInfo;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoundInfo $roundInfo)
    {
        $roundInfo->delete();
        return response()->noContent();
    }

    /**
     * Evaluate a user's answer.
     */
    public function evalAnswer(Request $request, RoundInfo $roundInfo)
    {
        $request->validate([
            'user_answer_id' => 'required|exists:user_answers,id',
        ]);

        $userAnswer = UserAnswer::find($request->user_answer_id);
        $score = $roundInfo->evalAnswer($userAnswer);

        return response()->json(['score' => $score]);
    }
}
