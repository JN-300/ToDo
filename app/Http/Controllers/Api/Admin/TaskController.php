<?php

namespace App\Http\Controllers\Api\Admin;

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

class TaskController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * @param FilterTaskRequest $request
     * @return TaskCollection
     */
    public function index(FilterTaskRequest $request):TaskCollection
    {
        $tasks = \App\Models\Task::query()
            ->when($request->has('with'), fn(Builder $builder)  => $builder->with($request->with))
            ->when(isset($request->filter['users']), fn(Builder $builder) => $builder->forUserIds(...$request->filter['users']))
            ->when(isset($request->filter['projects']), fn(Builder $builder) => $builder->forProjectIds(...$request->filter['projects']))
            ->when(isset($request->filter['overdue']), fn(Builder $builder) => $builder->overdue($request->filter['overdue']))
        ;

        $tasks = ($request->has('limit')
            ? $tasks->paginate($request->limit)
            : $tasks->get())
        ;

        return new TaskCollection($tasks);
    }

    public function show(FilterTaskRequest $request, Task $task):TaskResource
    {
        if ($request->has('with')) {
            $task->load($request->with);
        }
        return new TaskResource($task);
    }

    /**
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return TaskResource
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $task->load(['owner', 'project']);
        $task->update($request->all());
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
