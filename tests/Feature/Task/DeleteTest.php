<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTest extends TaskTestsAbstract
{
    /**
     * Testing an authenticated user can delete a task
     * - HTTP  Status should be 200
     * - Database count should one lower  as before calling
     * @return void
     */
    public function test_deleteTask():void
    {
        $taskCount = Task::all()->count();
        $task = Task::all()->first();

        $response =$this->destroyTask($task);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('success', true)
        ;
        $this->assertDatabaseCount(Task::class, $taskCount-1);
    }

    /**
     * Testing an unauthenticated user cannot delete a task
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     * - Database count should be same as before calling
     * @return void
     */
    public function test_couldNotDeleteTaskAsUnauthenticatedUser():void
    {
        $taskCount = Task::all()->count();
        $task = Task::all()->first();

        $response =  $this->deleteJson('api/tasks/'.$task->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.');

        $this->assertDatabaseCount(Task::class, $taskCount);
    }


    /* -------------------------------------------------------------------------------------------------------------- */


    /**
     * @param Task $task
     * @return \Illuminate\Testing\TestResponse
     */
    private function destroyTask(Task $task):TestResponse
    {
        $user = User::all()->first();
        Sanctum::actingAs($user);

        return $this->deleteJson('api/tasks/'.$task->id);

    }
}
