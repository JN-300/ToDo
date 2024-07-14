<?php

namespace Tests\Unit;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskOverdueTest extends TestCase
{
    use RefreshDatabase;

    const COUNT_TASKS_TODO = 20;
    const COUNT_TASKS_PROGRESS = 20;
    const COUNT_TASKS_DONE = 20;
    const COUNT_TASKS_OVERDUE_DONE = 10;
    const COUNT_TASKS_OVERDUE_TODO = 7;
    const COUNT_TASKS_OVERDUE_PROGRESS = 6;

    private ?User $owner = null;
    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        Task::factory(self::COUNT_TASKS_TODO)
            ->withOwner($this->owner)
            ->withRandomDeadline(startDate: '+30 minutes')
            ->create(['status' => TaskStatusEnum::TODO]);

        Task::factory(self::COUNT_TASKS_PROGRESS)
            ->withOwner($this->owner)
            ->withRandomDeadline(startDate: '+30 minutes')
            ->create(['status' => TaskStatusEnum::IN_PROGRESS]);

        Task::factory(self::COUNT_TASKS_DONE)
            ->withOwner($this->owner)
            ->withRandomDeadline(startDate: '+30 minutes')
            ->create(['status' => TaskStatusEnum::DONE]);

        Task::factory(self::COUNT_TASKS_OVERDUE_DONE)
            ->withOwner($this->owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::DONE]);

        Task::factory(self::COUNT_TASKS_OVERDUE_TODO)
            ->withOwner($this->owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::TODO]);

         Task::factory(self::COUNT_TASKS_OVERDUE_PROGRESS)
            ->withOwner($this->owner)
            ->withRandomDeadline(endDate: '-1 second')
            ->create(['status' => TaskStatusEnum::IN_PROGRESS]);

    }


    public function test_showAllTaskWithStatusDoTo():void
    {
        $tasks = Task::forUser($this->owner)
            ->byStatus(TaskStatusEnum::TODO)
            ->count()
        ;
        $count = (self::COUNT_TASKS_TODO + self::COUNT_TASKS_OVERDUE_TODO);
        $this->assertEquals($count, $tasks);
    }
    public function test_showAllTaskNotWithStatusDone():void
    {
        $tasks = Task::forUser($this->owner)
            ->notByStatus(TaskStatusEnum::DONE)
            ->count()
        ;
        $count = (self::COUNT_TASKS_TODO + self::COUNT_TASKS_OVERDUE_TODO + self::COUNT_TASKS_PROGRESS + self::COUNT_TASKS_OVERDUE_PROGRESS);
        $this->assertEquals($count, $tasks);
    }

    public function test_showAllOverdueTasksForUser():void
    {
        $tasks = Task::forUser($this->owner)
            ->overdue()
            ->count()
        ;
        $count = (self::COUNT_TASKS_OVERDUE_PROGRESS + self::COUNT_TASKS_OVERDUE_TODO + self::COUNT_TASKS_OVERDUE_DONE);
        $this->assertEquals($count, $tasks);
    }

    public function test_showAllOverdueTasksNotWithStatusDoneForUser():void
    {

        $tasks = Task::forUser($this->owner)
            ->notByStatus(TaskStatusEnum::DONE)
            ->overdue()
            ->count()
        ;
        $count = (self::COUNT_TASKS_OVERDUE_PROGRESS + self::COUNT_TASKS_OVERDUE_TODO);
        $this->assertEquals($count, $tasks);
    }

}
