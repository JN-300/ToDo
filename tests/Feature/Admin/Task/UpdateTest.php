<?php

namespace Tests\Feature\Admin\Task;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UpdateTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Simple test to check a admin can update any task
     * @return void
     */
    public function test_updateTaskOfUser():void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->withOwner($owner)->create();
        $admin = User::factory()->create(['admin' => true]);
        $newData = [
            'title' => 'My new title from admin'
        ];
         $this->updateTask($task, $newData, $admin)
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * Counter test to check non admins could not update a task from another user
     * @return void
     */
    public function test_couldNotUpdateTaskOfUserAsNonAdmin():void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->withOwner($owner)->create();
//        $nonAdmin = User::factory()->create(['admin' => false]);
        $newData = [
            'title' => 'My new title from admin'
        ];
        $this->updateTask($task, $newData, $owner)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test to check if an admin can update a task with an overdue date
     *
     * @return void
     */
    public function test_updateOverdueTask():void
    {
        $admin = User::factory()->create(['admin' => true]);
        $owner = User::factory()->create();
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::IN_PROGRESS]);
        $newData = [
            'title' => 'My new title from admin'
        ];
        $this->updateTask($task, $newData, $admin)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $newData['title'])
        ;
    }


    /**
     * Test to check if a admin can update the deadline of an overdue date
     * + bonus test to check if the owner can update his task after the deadline updated
     * @return void
     */
    public function test_updateOverdueTaskWithNewDeadline():void
    {
        $admin = User::factory()->create(['admin' => true]);
        $owner = User::factory()->create();
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::IN_PROGRESS]);
        $newData = [
            'deadline' => Carbon::now()->modify('+ 1day')->toIso8601String()
        ];
        $this->updateTask($task, $newData, $admin)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.deadline', $newData['deadline'])
        ;

        // bonus test user can re-edit task (see Feature/Task/UpdateTest.php:test_couldNotUpdateOverdueTask
        $newUserData = ['title' => 'my user title'];
        $this->actingAs($owner)
            ->patchJson('api/tasks/'.$task->id, $newUserData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $newUserData['title'])
            ;
    }


    /* -------------------------------------------------------------------------------------------------------------- */
    /**
     * @param Task $task
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    private function updateTask(Task $task, array $data, ?User $user = null):TestResponse
    {
        $user = $user ?? User::factory()->create();
        return $this
            ->actingAs($user)
            ->patchJson('/api/admin/tasks/'.$task->id, $data);
    }
}
