<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
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
    public function index():ProjectCollection
    {
        $projects = Project::all();
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
    public function show(Project $project):ProjectResource
    {
        return new ProjectResource($project);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->all());
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
