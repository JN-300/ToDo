<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskUserRelationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_createdTaskHasOwner(): void
    {
        $owner = User::factory()->create();
        $task = Task::factory()
            ->withOwner($owner)
            ->create()
        ;

        $this->assertInstanceOf(User::class, $task->owner);
        $this->assertEquals($task->owner->id, $owner->id);
    }
}
