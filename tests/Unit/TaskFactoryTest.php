<?php

namespace Tests\Unit;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class TaskFactoryTest extends TestCase
{
    const TEST_RUNS =100;

    use RefreshDatabase;
    public function test_createWithStartdateAndEnddateForRandomDeadline():void
    {
        $now = new \DateTimeImmutable('now');
        $tasks = Task::factory(self::TEST_RUNS)
            ->withRandomDeadline(startDate: '-1 second', endDate: '+1 second')
            ->make();

        $tasks->each(function(Task $task) use ($now) {
            $deadline = $task->deadline->format('Y-m-d H:i:s');
            $this->assertLessThanOrEqual($now->modify('+1 second')->format('Y-m-d H:i:s'),$deadline);
            $this->assertGreaterThanOrEqual($now->modify('-1 second')->format('Y-m-d H:i:s'),$deadline);
        });
    }

    public function test_createWithOnlyStartdateForRandomDeadline():void
    {
        $now = new \DateTimeImmutable('now');
        /** @var Collection $tasks */
        $tasks = Task::factory(self::TEST_RUNS)
            ->withRandomDeadline(startDate: '-1 second')
            ->make();

        $tasks->each(function(Task $task) use ($now) {
//            print $task->deadline->format('Y-m-d H:i:s').PHP_EOL;
            $deadline = $task->deadline->format('Y-m-d H:i:s');
            $this->assertGreaterThanOrEqual($now->modify('-1 second')->format('Y-m-d H:i:s'), $deadline);
            $this->assertLessThanOrEqual($now->modify('+1 year')->format('Y-m-d H:i:s'), $deadline);
        });
    }

    public function test_createWithOnlyEnddateForRandomDeadline():void
    {
        $now = new \DateTimeImmutable('now');
        /** @var Collection $tasks */
        $tasks = Task::factory(self::TEST_RUNS)
            ->withRandomDeadline(endDate: '-1 second')
            ->make();

        $tasks->each(function(Task $task) use ($now) {
            $deadline = $task->deadline->format('Y-m-d H:i:s');
            $this->assertLessThanOrEqual($now->modify('-1 second')->format('Y-m-d H:i:s'), $deadline);
        });
    }

    public function test_createWithRandomStatus():void
    {
        $testStatus = [
            TaskStatusEnum::TODO,
            TaskStatusEnum::IN_PROGRESS
        ];
        /** @var Collection $tasks */
        $tasks = Task::factory(self::TEST_RUNS)
            ->withRandomStatus(...$testStatus)
            ->make();
        $tasks->each(function(Task $task) use ($testStatus) {
//            print $task->status->value.PHP_EOL;
            $this->assertTrue(in_array($task->status, $testStatus));
        });

    }
}
