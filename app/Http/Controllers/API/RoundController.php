<?php

namespace App\Http\Controllers\API;

use App\Models\Round;
use Illuminate\Http\Request;

class RoundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Round::with(['game', 'roundInfo', 'userScores'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'round_info_id' => 'required|exists:round_infos,id',
        ]);

        return Round::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Round $round)
    {
        return $round->load(['game', 'roundInfo', 'userScores']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Round $round)
    {
        $request->validate([
            'round_info_id' => 'sometimes|exists:round_infos,id',
        ]);

        $round->update($request->all());
        return $round;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Round $round)
    {
        $round->delete();
        return response()->noContent();
    }

    /**
     * Calculate user scores for the round.
     */
    public function calculateScores(Round $round)
    {
        $round->calculateUserScore();
        return response()->json(['message' => 'Scores calculated']);
    }
}
