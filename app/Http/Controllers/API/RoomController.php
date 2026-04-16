<?php

namespace App\Http\Controllers\API;

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        // Get rooms where user is owner or member
        $ownedRooms = $user->ownedRooms()->with('creator')->get();
        $memberRooms = $user->memberRooms()->with('creator')->get();
        $pendingRooms = $user->pendingRoomInvitations()->with('creator')->get();

        $rooms = $ownedRooms->merge($memberRooms)->merge($pendingRooms);

        return response()->json([
            'success' => $rooms,
            'message' => 'Rooms retrieved successfully.'
        ]);
    }

    /**
     * Show a specific room
     */
    public function show($id)
    {
        $room = Room::with(['users', 'creator', 'gameSetting'])->findOrFail($id);

        $this->authorize('view', $room);

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
        $this->authorize('create', Room::class);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room = Room::create([
            'name' => $request->name,
            'creator_id' => Auth::id(),
        ]);

        // Add creator as owner
        $room->users()->attach(Auth::id(), [
            'role' => 'owner',
            'status' => 'accepted',
            'invited_at' => now(),
            'invited_by' => Auth::id()
        ]);

        return response()->json([
            'success' => $room->load(['users', 'creator']),
            'message' => 'Room created successfully.'
        ], 201);
    }

    /**
     * Update a room (only by creator)
     */
    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $this->authorize('update', $room);

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

        $this->authorize('delete', $room);

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully.'
        ]);
    }

    /**
     * Invite a user to the room
     */
    public function inviteUser(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);

        $this->authorize('invite', $room);

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is already in the room
        if ($room->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'error' => 'User is already a member of this room.'
            ], 400);
        }

        // Add user with pending status
        $room->users()->attach($user->id, [
            'role' => 'member',
            'status' => 'pending',
            'invited_at' => now(),
            'invited_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User invited successfully.'
        ]);
    }

    /**
     * Accept room invitation
     */
    public function acceptInvitation($roomId)
    {
        $room = Room::findOrFail($roomId);
        $user = Auth::user();

        // Check if user has a pending invitation
        $membership = $room->users()->where('user_id', $user->id)->where('status', 'pending')->first();

        if (!$membership) {
            return response()->json([
                'error' => 'No pending invitation found.'
            ], 404);
        }

        $room->users()->updateExistingPivot($user->id, ['status' => 'accepted']);

        return response()->json([
            'success' => true,
            'message' => 'Invitation accepted successfully.'
        ]);
    }

    /**
     * Decline room invitation
     */
    public function declineInvitation($roomId)
    {
        $room = Room::findOrFail($roomId);
        $user = Auth::user();

        // Check if user has a pending invitation
        $membership = $room->users()->where('user_id', $user->id)->where('status', 'pending')->first();

        if (!$membership) {
            return response()->json([
                'error' => 'No pending invitation found.'
            ], 404);
        }

        $room->users()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Invitation declined successfully.'
        ]);
    }

    /**
     * Remove user from room (by room owner)
     */
    public function removeUser(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);

        $this->authorize('manageInvitations', $room);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        // Cannot remove the owner
        if ($userId == $room->creator_id) {
            return response()->json([
                'error' => 'Cannot remove room owner.'
            ], 400);
        }

        $room->users()->detach($userId);

        return response()->json([
            'success' => true,
            'message' => 'User removed from room successfully.'
        ]);
    }
}
