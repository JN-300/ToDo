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
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TasKNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Integration test to check if an update trigger an event
     * and also the event listener is registered
     * @return void
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

    /**
     * Integration test Motification will dispatch if task updated with a deadline lower now
     * @return void
     */
    public function test_taskUpdateWithOverdueDeadlineShouldTriggerANotification()
    {
        Notification::fake();
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
        Notification::assertSentTo($owner, SendOverdueStatusMail::class);
    }


    /**
     * Integration test that no notification dispatched if task updated with a deadline greater/equal now
     * @return void
     */
    public function test_taskUpdateWithFutureDeadlineShouldNotTriggerANotification()
    {
        Notification::fake();
        $owner = User::factory()->create();
        $admin = User::factory()->create(['admin' => true]);
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(startDate: '+30 second')
            ->create(['status' => TaskStatusEnum::TODO])
        ;
        $this
            ->actingAs($admin)
            ->patchJson('api/tasks/'.$task->id, ['title' => 'test'])
            ->assertStatus(Response::HTTP_OK)
        ;
        Notification::assertNotSentTo($owner, SendOverdueStatusMail::class);

    }

    /**
     * Integration test that no notification dispatched if task updated with a deadline lower  now
     * and a DONE status
     * @return void
     */
    public function test_taskUpdateWithOverdueDeadlineShouldNotTriggerANotificationIfStatusIsDone()
    {
        Notification::fake();
        $owner = User::factory()->create();
        $admin = User::factory()->create(['admin' => true]);
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::DONE])
        ;
        $this
            ->actingAs($admin)
            ->patchJson('api/tasks/'.$task->id, ['title' => 'test'])
            ->assertStatus(Response::HTTP_OK)
        ;
        Notification::assertNotSentTo($owner, SendOverdueStatusMail::class);
    }
}
