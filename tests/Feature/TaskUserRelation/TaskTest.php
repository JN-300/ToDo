<?php

namespace Tests\Feature\TaskUserRelation;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing Current User can list all tasks for his own userID
     * route make for std user no sense, 'cause the get ./api/tasks route does the same
     * just prepared for planned admin access
     *
     * - HTTP Status should be 200
     * - response data array with task data
     *
     * @return void
     */
    public function test_listAllTasksByUser():void
    {

        $projects    = Project::factory(4)->create();
        $user       = User::factory()->create();
        $otherUsers = User::factory(10)->create();


        Task::factory(15)
            ->withOwner($user)
            ->withOneOfGivenProjects($projects)
            ->create();

        Task::factory(100)
            ->withOneOfGivenOwner($otherUsers)
            ->withOneOfGivenProjects($projects)
            ->create();

        Sanctum::actingAs($user);
        $response = $this->getJson('api/tasks/user/'.$user->id);
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(15, 'data')
        ;
    }


    /**
     * Testing a normal user cannot list tasks for another user
     *
     * - HTTP Status should be 403
     * @return void
     */
    public function test_couldNotListAllTasksByOtherUser():void
    {
        $projects   = Project::factory(4)->create();
        $user       = User::factory()->create();
        $otherUsers = User::factory(10)->create();


        Task::factory(15)
            ->withOwner($user)
            ->withOneOfGivenProjects($projects)
            ->create();

        Task::factory(100)
            ->withOneOfGivenOwner($otherUsers)
            ->withOneOfGivenProjects($projects)
            ->create();

        $normalOtherUser = User::factory()->create();
        Sanctum::actingAs($normalOtherUser);
        $response = $this->getJson('api/tasks/user/'.$user->id);
        $response
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }

    public function test_couldNotUpdateTaskFromOtherUser():void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()
            ->withOwner($otherUser)
            ->create();

        $this->actingAs($user)
            ->patchJson('api/tasks/'.$task->id, ['title' => 'new title'])
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }


    public function test_couldNotDeleteTaskFromOtherUser():void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()
            ->withOwner($otherUser)
            ->create();

        $this->actingAs($user)
            ->deleteJson('api/tasks/'.$task->id)
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }
}
