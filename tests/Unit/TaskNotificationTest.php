<?php

namespace Tests\Unit;

use App\Enums\TaskStatusEnum;
use App\Events\Tasks\TaskUpdated;
use App\Listeners\SendOverdueTaskNotification;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\SendOverdueStatusMail;
use App\Observers\TaskObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskNotificationTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Testing event will be triggered after task update
     * @return void
     */
    public function test_taskUpdateShouldBeTriggerAnEvent()
    {
        Event::fake(TaskUpdated::class);
        $owner = User::factory()->create();
        $admin = User::factory()->create(['admin' => true]);
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::TODO])
        ;

        $task->update(['title' => 'new title']);
        Event::assertDispatched(TaskUpdated::class);
    }

    /**
     * Testing notifcation will be send after event dispatched
     * @return void
     */
    public function test_sendNotificationToUser():void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::TODO])
        ;
        $event = new TaskUpdated($task);
        $listener = new SendOverdueTaskNotification();
        $listener->handle($event);

        Notification::assertSentTo($owner,
            SendOverdueStatusMail::class,
            function($notification, $channels) use($owner, $task) {
                $this->assertContains('mail', $channels);
                $emailNotification = (object)$notification->toMail($owner);
                $this->assertEquals('Aufgabe wurde durch einen Admin bearbeitet', $emailNotification->subject);
                $this->assertTrue(Str::contains(implode(PHP_EOL, $emailNotification->introLines), $task->title));
                return true;
            }

        );
    }


    /**
     * Testing no notifocation will be send after update a not overdue task
     * @return void
     */
    public function test_dontSendNotificationToUserIfTaskNotOverdue():void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $task = Task::factory()
            ->withOwner($owner)
            ->withRandomDeadline(startDate: '+30 minutes')
            ->create(['status' => TaskStatusEnum::TODO])
        ;
        $event = new TaskUpdated($task);
        $listener = new SendOverdueTaskNotification();
        $listener->handle($event);

        Notification::assertNotSentTo($owner, SendOverdueStatusMail::class);
    }
}
