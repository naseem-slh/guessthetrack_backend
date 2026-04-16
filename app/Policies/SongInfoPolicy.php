an you give me the front<?php

namespace App\Policies;

use App\Models\SongInfo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SongInfoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SongInfo $songInfo): bool
    {
        return $songInfo->game->room->users()->where('user_id', $user->id)->where('status', 'accepted')->exists() ||
               $songInfo->game->room->creator_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SongInfo $songInfo): bool
    {
        return $songInfo->game->room->creator_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SongInfo $songInfo): bool
    {
        return $songInfo->game->room->creator_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SongInfo $songInfo): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SongInfo $songInfo): bool
    {
        return false;
    }
}
