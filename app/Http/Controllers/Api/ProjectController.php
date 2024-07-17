<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterProjectRequest;
use App\Http\Requests\FilterTaskRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilterProjectRequest $request):ProjectCollection
    {
        $projects = Project::query()
            ->when($request->has('with'), fn(Builder $builder) => $builder->with($request->with))
        ;
        $projects = ($request->has('limit')
            ? $projects->paginate($request->limit)
            : $projects->get())
        ;

        return new ProjectCollection($projects);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request):ProjectResource
    {
        $project = Project::create($request->all());
        return (new ProjectResource($project))
            ->additional([
                'success' => true,
                'message' => 'Project successfully created'
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(FilterProjectRequest $request, Project $project):ProjectResource
    {

        if ($request->has('with')) {
            $project->load($request->with);
        }
        return new ProjectResource($project);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->all());
        $project->load('tasks');
        return (new ProjectResource($project))
            ->additional([
                'success' => true,
                'message' => 'Project successfully updated'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['success' => true, 'message' => 'Project successfully deleted']);
    }
}
