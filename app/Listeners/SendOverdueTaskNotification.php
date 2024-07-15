<?php

namespace App\Listeners;


use App\Enums\TaskStatusEnum;
use App\Events\Tasks\TaskUpdated;
use App\Models\User;
use App\Notifications\SendOverdueStatusMail;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SendOverdueTaskNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskUpdated $event): void
    {
        $task = $event->task;
        /** @var User $owner */
        $owner = $event->task->owner;
        $now = Carbon::now();

        if (
            $task->deadline <= $now
            && in_array($task->status, TaskStatusEnum::notDone())
        ) {
            $owner->notify(new SendOverdueStatusMail($task));
        }
    }
}
