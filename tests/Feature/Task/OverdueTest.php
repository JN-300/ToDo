<?php

namespace Tests\Feature\Task;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OverdueTest extends TestCase
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

    /**
     * Testing listing only overdue tasks for current user
     *
     * - HTTP Status:200
     * @return void
     */
    public function test_listingOverdueTask(): void
    {
        $this->readOverdueTasks(user: $this->owner)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount((self::COUNT_TASKS_OVERDUE_TODO + self::COUNT_TASKS_OVERDUE_PROGRESS), 'data')
        ;

    }

    /**
     * Testing that an unauthenticated user cannot list any overdue tasks
     *
     * - HTTP Status: 401
     *
     * @return void
     */
    public function test_couldNotListOverdueTaskAsUnauthenticatedUser(): void
    {
        $this->getJson('api/tasks/overdue')
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ;

    }




    private function readOverdueTasks( ?User $user = null):TestResponse
    {
        $user = $user ?? User::factory()->create();
        Sanctum::actingAs($user);
        return $this->getJson('api/tasks/overdue');

    }
}
