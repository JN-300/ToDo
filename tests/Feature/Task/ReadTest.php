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
        $owner      = User::factory()->create();
        $tasks      = Task::factory(10)
            ->withOwner($owner)
            ->create();
        $taskCount = Task::all()->count();
        $response = $this->readTasks(user: $owner);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', fn(array $data) => count($data) === $taskCount)
        ;
    }

    /**
     * Testing an authenticated user recieves only his tasks
     *
     * - HTTP Status: 200
     * @return void
     */
    public function test_listOnlyMyTasks():void
    {
        // create 10 other User
        $users = User::factory(10)->create();
        // create 100 task for the other users
        Task::factory(100)
            ->withOneOfGivenOwner($users)
            ->create();

        // create test user
        $user = User::factory()->create();
        // create 13 example tasks for this user
        Task::factory(13)->withOwner($user)->create();

        // tests
        // test task count
        $this->assertDatabaseCount('tasks', 113);

        $this->readTasks(user: $user)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(13, 'data');

    }

    /**
     * Testing, that an unauthenticated user could not list any tasks
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     * @return void
     */
    public function test_couldNotListTasksAsUnauthenticatedUser():void
    {
        $owner = User::factory()->create();
        Task::factory(10)->withOwner($owner)->create();

        $this->getJson('api/tasks')
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
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
        $owner  = User::factory()->create();
        $task   = Task::factory()->withOwner($owner)->create();
        $this->readTasks(task: $task, user: $owner)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $task->title)
            ->assertJsonPath('data.description', $task->description)
            ->assertJsonPath('data.status', $task->status->value)
        ;
    }

    /**
     * Testing a authenticated user cannot show task detail of a task of another user
     *
     * - HTTP Status: 403
     * @return void
     */
    public function test_couldNotShowTaskOfOtherUser():void
    {
        $owner  = User::factory()->create();
        $task   = Task::factory()->withOwner($owner)->create();

        $otherUser = User::factory()->create();
        $this->readTasks(task: $task, user: $otherUser)
            ->assertStatus(Response::HTTP_FORBIDDEN)
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
        $owner = User::factory()->create();
        $task = Task::factory()->withOwner($owner)->create();
        $this->getJson('api/tasks/'.$task->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.');
    }


    /* -------------------------------------------------------------------------------------------------------------- */


    private function readTasks(?Task $task = null, ?User $user = null):TestResponse
    {
        $user = $user ?? User::factory()->create();
        Sanctum::actingAs($user);

        return ($task)
            ? $this->getJson('api/tasks/'.$task->id)
            : $this->getJson('api/tasks')
            ;

    }
}
