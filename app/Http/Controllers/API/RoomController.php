<?php

namespace App\Http\Controllers\API;

use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $rooms = $user->rooms()->with('creator')->get(); // include creator info

        return response()->json([
            'success' => $rooms,
            'message' => 'Rooms retrieved successfully.'
        ]);    }

    /**
     * Show a specific room
     */
    public function show($id)
    {

        $room = Room::with('users', 'creator')->findOrFail($id);
        Log::info(Auth::id());        // Optional: only allow if user belongs to this room
        if (!$room->users->contains(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json([
            'success' => $room,
            'message' => 'Room retrieved successfully.'
        ]);
    }

    /**
     * Store a new room
     */
    public function store(Request $request)
    {
        Log::info(Auth::id());        // Optional: only allow if user belongs to this room

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room = Room::create([
            'name' => $request->name,
            'creator_id' => Auth::id(),
        ]);

        // Add creator as a member
        $room->users()->attach(Auth::id());

        return response()->json([
            'success' => $room,
            'message' => 'Room created successfully.'
        ], 201);
    }

    /**
     * Update a room (only by creator)
     */
    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        if ($room->creator_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room->update(['name' => $request->name]);

        return response()->json([
            'success' => $room,
            'message' => 'Room updated successfully.'
        ]);
    }

    /**
     * Delete a room (only by creator)
     */
    public function destroy($id)
    {
        $room = Room::findOrFail($id);

        if ($room->creator_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully.'
        ]);
    }


}
