<?php

namespace App\Http\Controllers\API;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get games from rooms the user has access to
        $user = auth()->user();
        $roomIds = $user->rooms()->wherePivot('status', 'accepted')->pluck('rooms.id');
        $ownedRoomIds = $user->ownedRooms()->pluck('id');
        $accessibleRoomIds = $roomIds->merge($ownedRoomIds);

        return Game::whereIn('room_id', $accessibleRoomIds)
            ->with(['room', 'gameSetting', 'rounds'])
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'game_setting_id' => 'required|exists:game_settings,id',
        ]);

        $game = Game::create($request->all());

        $this->authorize('create', $game);

        return $game->load(['room', 'gameSetting']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        $this->authorize('view', $game);

        return $game->load(['room', 'gameSetting', 'rounds']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        $this->authorize('update', $game);

        $request->validate([
            'game_setting_id' => 'sometimes|exists:game_settings,id',
        ]);

        $game->update($request->all());
        return $game->load(['room', 'gameSetting']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        $this->authorize('delete', $game);

        $game->delete();
        return response()->noContent();
    }

    /**
     * Get total scores for the game.
     */
    public function totalScores(Game $game)
    {
        $this->authorize('view', $game);

        return response()->json($game->calculateTotalScores());
    }
}
