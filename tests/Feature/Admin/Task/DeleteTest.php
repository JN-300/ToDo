<?php

namespace Feature\Admin\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Simple test to check if admin can delete any task
     * @return void
     */
    public function test_deleteTaskFromOtherUser():void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->withOwner($owner)->create();
        $admin = User::factory()->create(['admin' => true]);

        $this
            ->actingAs($admin)
            ->deleteJson('api/admin/tasks/'.$task->id)
            ->assertStatus(Response::HTTP_OK)
            ;
    }

    /**
     * Counter test to check that a non admin could not delete a task from another user
     * @return void
     */
    public function test_couldNotDeleteTaskFromOtherUserAsNonAdmin():void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->withOwner($owner)->create();
        $admin = User::factory()->create(['admin' => false]);

        $this
            ->actingAs($admin)
            ->deleteJson('api/admin/tasks/'.$task->id)
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }


}
