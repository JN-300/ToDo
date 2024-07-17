<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
    public function index(FilterTaskRequest $request):TaskCollection
    {
        $tasks = Task::query()
            ->when(isset($request->filter['overdue']), fn(Builder $builder) => $builder->overdue($request->filter['overdue']))
            ->when($request->has('with'), fn(Builder $builder)  => $builder->with($request->with))
            ->forUser(Auth::user())
        ;

        $tasks = ($request->has('limit')
            ? $tasks->paginate($request->limit)
            : $tasks->get())
        ;

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
    public function show(FilterTaskRequest $request, Task $task):TaskResource
    {
        if ($request->has('with')) {
            $task->load($request->with);
        }
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
