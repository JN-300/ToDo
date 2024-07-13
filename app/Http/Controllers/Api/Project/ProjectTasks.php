<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskCollection;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;

class ProjectTasks extends \Illuminate\Routing\Controller
{

    public function __invoke(Project $project):TaskCollection
    {
        if (request()->user()->cannot('view', $project)) {
            abort(403);
        }
        $tasks = $project->tasks;
        return (new TaskCollection($tasks))
            ->additional([
                'project' => new ProjectResource($project->withoutRelations())
            ])
            ;
    }
}
