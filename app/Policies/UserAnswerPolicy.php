<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Auth\Access\Response;

class UserAnswerPolicy
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
    public function view(User $user, UserAnswer $userAnswer): bool
    {
        // Users can view their own answers
        if ($userAnswer->user_id === $user->id) {
            return true;
        }

        // Room owners can view all answers in their rooms
        return $userAnswer->roundInfo->round->game->room->creator_id === $user->id;
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
    public function update(User $user, UserAnswer $userAnswer): bool
    {
        // Users can only update their own answers
        return $userAnswer->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserAnswer $userAnswer): bool
    {
        // Users can only delete their own answers, or room owners can delete any
        return $userAnswer->user_id === $user->id ||
               $userAnswer->roundInfo->round->game->room->creator_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserAnswer $userAnswer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserAnswer $userAnswer): bool
    {
        return false;
    }
}
