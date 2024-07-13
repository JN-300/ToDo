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

class ReadTest extends TaskTestsAbstract
{

    /**
     * Testing of listing all available tasks
     *
     * - HTTP Status should be 200
     * - Response should have an array with key data and an amount of the task ->count
     *
     * @return void
     */
    public function test_listTasks():void
    {
        $taskCount = Task::all()->count();
        $response = $this->readTasks();
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', fn(array $data) => count($data) === $taskCount)
        ;
    }

    /**
     * Testing, that an unauthenticated user could not list any tasks
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     * @return void
     */
    public function test_couldNotListTasksAsUnauthenticatedUser():void
    {
        $response = $this->getJson('api/tasks');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.');
        ;
    }


    /**
     * Testing that an authenticated user can show a task
     * - HTTP Status should be 200
     * - Response should be an array with the relevant task data (title, description, status, ...).
     *
     * @return void
     */
    public function test_showTask():void
    {
        $task = Task::all()->first();
        $response = $this->readTasks($task);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $task->title)
            ->assertJsonPath('data.description', $task->description)
            ->assertJsonPath('data.status', $task->status)
        ;
    }

    /**
     * Testing that an unauthenticated user could not show a task
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     *
     * @return void
     */
    public function test_couldNotShowTaskAsUnauthenticatedUser():void
    {
        $task = Task::all()->first();
        $response = $this->getJson('api/tasks/'.$task->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.');
    }


    /* -------------------------------------------------------------------------------------------------------------- */


    private function readTasks(?Task $task = null):TestResponse
    {
        $user = User::all()->first();
        Sanctum::actingAs($user);

        return ($task)
            ? $this->getJson('api/tasks/'.$task->id)
            : $this->getJson('api/tasks')
            ;

    }
}
