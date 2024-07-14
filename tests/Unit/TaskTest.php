<?php

namespace Tests\Unit;

use App\Enums\TaskStatusEnum;
use App\Exceptions\OwnerException;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing create task through factory
     * @return void
     */
    public function test_factoryCreate():void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->create(['owner_id' => $owner->id]);
        $this->assertTrue($task instanceof Task);
        $this->assertTrue(Str::isUuid($task->id));
    }

    /**
     * Testing create task
     * @return void
     */
    public function test_createTask():void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $taskData = [
            'title' => 'ich bin der Titel des Tasks',
            'description' => 'ich bin die Beschreibung des Tasks',
            'status' => TaskStatusEnum::TODO->value,
            'deadline' => Carbon::now()->modify('+1 day'),
            'project_id' => Project::factory()->create()->id
        ];

        $task = Task::create($taskData);
        $this->assertTrue($task instanceof Task);
    }

    /**
     * Testing reading a task
     * @return void
     */
    public function test_readTask():void
    {
        $owner  = User::factory()->create();
        Sanctum::actingAs($owner);
        $task   = Task::factory()->create();

        $readTask   = Task::find($task->id)->first();
        $this->assertEquals($task->title, $readTask->title);
    }

    /**
     * Testing updating a task
     * @return void
     */
    public function test_updateTask():void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $task = Task::factory()->create();
        $newData = [
            'title' => 'My updated title'
        ];
        $task->update($newData);
        $updatedTask = Task::find($task->id)->first();
        $this->assertEquals($task->title, $updatedTask->title);
        $this->assertEquals($updatedTask->title, $newData['title']);
    }

    /**
     * Testing deleting a task
     * @return void
     */
    public function test_deleteTask():void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $task = Task::factory()->create();
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
        $task->delete();
        $this->assertDatabaseMissing('tasks', ['id', $task->id]);
    }

    /**
     * Testing a created task has an owner
     * @return void
     */
    public function test_createdTaskMustHaveAnOwner():void
    {
        $user = User::factory()->create();
        $task = Task::factory()
            ->withOwner($user)
            ->create();

        $this->assertInstanceOf(User::class, $task->owner);
        $this->assertEquals($task->owner->id, $user->id);
    }

    /**
     * Testing Task could not create without an owner
     * @return void
     */
    public function test_couldNotCreateATaskWithoutAnOwner():void
    {
        $this->expectException(OwnerException::class);
        Task::factory()->create();

    }
}
