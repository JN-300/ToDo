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
        $owner      = User::factory()->create();
        $task       = Task::factory()
            ->withOwner($owner)
            ->create();
        // testing if task created
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
        // testing destroy route
        $this->destroyTask($task, user: $owner)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('success', true)
        ;
        // testing if task deleted
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
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
        $owner      = User::factory()->create();
        $task      = Task::factory()
            ->withOwner($owner)
            ->create();

        $this->deleteJson('api/tasks/'.$task->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.');

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }


    /* -------------------------------------------------------------------------------------------------------------- */


    /**
     * @param Task $task
     * @return \Illuminate\Testing\TestResponse
     */
    private function destroyTask(Task $task, ?User $user = null):TestResponse
    {
        $user = $user ?? User::factory()->create();
        Sanctum::actingAs($user);

        return $this->deleteJson('api/tasks/'.$task->id);

    }
}
