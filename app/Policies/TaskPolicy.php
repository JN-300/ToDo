<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{

    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin())
        {
            return true;
        }
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user
            ? Response::allow()
            : Response::deny()
            ;
    }

    public function viewAnyOfOtherUser(User $user, User $otherUser): Response
    {
        return $user && $otherUser->id === $user->id
            ? Response::allow()
            : Response::deny()
            ;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): Response
    {
        return ($user && $task->owner->id === $user->id)
            ? Response::allow()
            : Response::deny()
            ;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user
            ? Response::allow()
            : Response::deny()
            ;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): Response
    {
        $now = Carbon::now();
        return ($user && $task->owner->id === $user->id)
            ? (($task->deadline > $now)
                ? Response::allow()
                : Response::deny('Update of overdue tasks not allowed.')
            )
            : Response::deny()
            ;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): Response
    {
        return ($user && $task->owner->id === $user->id)
            ? Response::allow()
            : Response::deny()
            ;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        //
    }
}
