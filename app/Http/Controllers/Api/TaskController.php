<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * @return TaskCollection
     */
    public function index():TaskCollection
    {
        $tasks = Task::forUser(Auth::user())->get();
        // TODO: Create as FilterRequest with configurable 'with' params
        if (request()->query->has('with')) {
            $loadRelations = request()->query('with');
            if (!is_array($loadRelations))
            {
                $loadRelations = explode(',', $loadRelations);
            }
            $loadRelations = array_filter($loadRelations, fn($item) => $item == 'project');
            $tasks->load($loadRelations);
        }

        return new TaskCollection($tasks);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request):TaskResource
    {
        /** @var Task $task */
        $task = Task::create($request->all());
        $task->load('project');
        return (new TaskResource($task))
            ->additional([
                'success' => true,
                'message' => 'Task successfully generated'
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task):TaskResource
    {
        $task->load('project');
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task):TaskResource
    {
        $task->update($request->all());
        $task->load('project');
        return (new TaskResource($task))
            ->additional([
                'success' => true,
                'message' =>  'Task successfully updated'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true, 'message' => 'Task successfully deleted']);
    }
}
