<?php

namespace App\Http\Controllers\API;

use App\Models\GameSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GameSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get game settings from rooms the user has access to
        $user = auth()->user();
        $roomIds = $user->rooms()->wherePivot('status', 'accepted')->pluck('rooms.id');
        $ownedRoomIds = $user->ownedRooms()->pluck('rooms.id');
        logger()->info('User ID: ' . $user->id);
        logger()->info('Room IDs from member rooms: ' . $roomIds);
        logger()->info('Room IDs from owned rooms: ' . $ownedRoomIds);
        $accessibleRoomIds = $roomIds->merge($ownedRoomIds);
        logger()->info('Accessible Room IDs: ' . $accessibleRoomIds);

        return GameSetting::whereIn('room_id', $accessibleRoomIds)->with('room')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'rounds_count' => 'required|integer|min:1',
            'genre' => 'required|string',
        ]);

        // Check if user owns the room before creating the game setting
        $user = auth()->user();
        $room = \App\Models\Room::findOrFail($request->room_id);
        
        if ($room->creator_id !== $user->id) {
            return response()->json(['error' => 'You can only create game settings for rooms you own.'], 403);
        }

        $gameSetting = GameSetting::create($request->all());

        return $gameSetting->load('room');
    }

    /**
     * Display the specified resource.
     */
    public function show(GameSetting $gameSetting)
    {
        $this->authorize('view', $gameSetting);

        return $gameSetting->load('room');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GameSetting $gameSetting)
    {
        $this->authorize('update', $gameSetting);

        $request->validate([
            'rounds_count' => 'sometimes|integer|min:1',
            'genre' => 'sometimes|string',
        ]);

        $gameSetting->update($request->all());
        return $gameSetting->load('room');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameSetting $gameSetting)
    {
        $this->authorize('delete', $gameSetting);

        $gameSetting->delete();
        return response()->noContent();
    }
}
