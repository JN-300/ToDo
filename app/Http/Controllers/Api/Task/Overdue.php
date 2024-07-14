<?php

namespace App\Http\Controllers\Api\Task;

use App\Enums\TaskStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Models\Task;
use Illuminate\Http\Request;

class Overdue extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $tasks = Task::forUser($request->user())
            ->notByStatus(TaskStatusEnum::DONE)
            ->overdue()
            ->get();

        return new TaskCollection($tasks);
    }
}
