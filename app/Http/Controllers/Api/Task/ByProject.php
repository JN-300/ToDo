<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskCollection;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * SingleActionController to return all available task for given project
 */
class ByProject extends Controller
{

    /**
     * @param Project $project
     * @return TaskCollection
     */
    public function __invoke(Project $project):TaskCollection
    {
        if (request()->user()->cannot('view', $project)) {
            abort(Response::HTTP_FORBIDDEN);
        }
        if (request()->user()->cannot('viewAny', Task::class)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $tasks = $project
            ->tasks()
            ->forUser(Auth::user())
            ->get()
        ;
        return (new TaskCollection($tasks))
            ->additional([
                'project' => new ProjectResource($project->withoutRelations())
            ])
            ;
    }
}
