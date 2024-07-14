<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskCollection;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * SingleActionController to return all available task for given user
 */
class ByUser extends Controller
{
    /**
     * @param User $user
     * @return TaskCollection
     */
    public function __invoke(User $user):TaskCollection
    {
        if (request()->user()->cannot('viewAnyOfOtherUser',[Task::class, $user])) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $tasks = Task::forUser($user)->get()
        ;
        return new TaskCollection($tasks);
    }
}
