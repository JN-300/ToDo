<?php

namespace Tests\Feature;

use App\Enums\TaskStatusEnum;
use App\Events\Tasks\TaskUpdated;
use App\Listeners\SendOverdueTaskNotification;
use App\Models\Task;
use App\Models\User;
use App\Notifications\SendOverdueStatusMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TasKNotificationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_taskUpdateShouldBeTriggerAnEvent(): void
    {
        Event::fake(TaskUpdated::class);
        $owner = User::factory()->create();
        $admin = User::factory()->create(['admin' => true]);
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::TODO])
        ;

        $this
            ->actingAs($admin)
            ->patchJson('api/tasks/'.$task->id, ['title' => 'test'])
            ->assertStatus(Response::HTTP_OK)
            ;
        Event::assertDispatched(TaskUpdated::class);
        Event::assertListening(TaskUpdated::class, SendOverdueTaskNotification::class);
    }

    public function test_taskUpdateWithOverdueDeadlineShouldTriggerANotification()
    {
//        Event::fake(TaskUpdated::class);
        Notification::fake();
        $owner = User::factory()->create();
        $admin = User::factory()->create(['admin' => true]);
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::TODO])
        ;

        $this->assertEquals($task->status, TaskStatusEnum::TODO);

        $this
            ->actingAs($admin)
            ->patchJson('api/tasks/'.$task->id, ['title' => 'test'])
            ->assertStatus(Response::HTTP_OK)
        ;
        Notification::assertSentTo($owner, SendOverdueStatusMail::class);

    }


    public function test_taskUpdateWithFutureDeadlineShouldNotTriggerANotification()
    {
//        Event::fake(TaskUpdated::class);
        Notification::fake();
        $owner = User::factory()->create();
        $admin = User::factory()->create(['admin' => true]);
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(startDate: '+30 second')
            ->create(['status' => TaskStatusEnum::TODO])
        ;

        $this->assertEquals($task->status, TaskStatusEnum::TODO);

        $this
            ->actingAs($admin)
            ->patchJson('api/tasks/'.$task->id, ['title' => 'test'])
            ->assertStatus(Response::HTTP_OK)
        ;
        Notification::assertNotSentTo($owner, SendOverdueStatusMail::class);

    }

    public function test_taskUpdateWithOverdueDeadlineShouldNotTriggerANotificationIfStatusIsDone()
    {
//        Event::fake(TaskUpdated::class);
        Notification::fake();
        $owner = User::factory()->create();
        $admin = User::factory()->create(['admin' => true]);
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::DONE])
        ;

        $this->assertEquals($task->status, TaskStatusEnum::DONE);

        $this
            ->actingAs($admin)
            ->patchJson('api/tasks/'.$task->id, ['title' => 'test'])
            ->assertStatus(Response::HTTP_OK)
        ;
        Notification::assertNotSentTo($owner, SendOverdueStatusMail::class);

    }
}
