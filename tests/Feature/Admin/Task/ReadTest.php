<?php

namespace Feature\Admin\Task;

use App\Enums\TaskStatusEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReadTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Testing of listing all available tasks
     *
     * - HTTP Status should be 200
     * - Response should have an array with key data and an amount of the task ->count
     *
     * @return void
     */
    public function test_listTasks():void
    {
        $users      = User::factory(20)->create();
        $projects = Project::factory(10)->create();
        Task::factory(100)
            ->withOneOfGivenOwner($users)
            ->withOneOfGivenProjects($projects)
            ->create();

        $admin = User::factory()
            ->create(['admin' => true]);
        $taskCount = Task::all()->count();
        $this->readTasks(user: $admin)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', fn(array $data) => count($data) === $taskCount)
        ;
    }


    public function test_listSingleTask():void
    {
        $admin = User::factory()->create(['admin' => true]);
        $owner = User::factory()->create();
        $task = Task::factory()
            ->withOwner($owner)
            ->create(['status' => TaskStatusEnum::IN_PROGRESS]);
        $this->readTasks(task: $task, user: $admin)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $task->title)
            ;
    }
    /**
     * Counter-test no admins could not list all tasks
     * @return void
     */
    public function test_nonAdminCouldNotListTasks():void
    {
        $users = User::factory(20)->create();
        $projects = Project::factory(10)->create();
        Task::factory(100)
            ->withOneOfGivenOwner($users)
            ->withOneOfGivenProjects($projects)
            ->create();

        $nonAdmin = User::factory()
            ->create(['admin' => false]);

        $this->readTasks(user: $nonAdmin)
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }


    /**
     * Filter query test to test an admin could filter all tasks for one user
     *
     * @return void
     */
    public function test_listTasksOfASingleUser():void
    {
        $users      = User::factory(20)->create();
        $projects = Project::factory(10)->create();
        Task::factory(100)
            ->withOneOfGivenOwner($users)
            ->withOneOfGivenProjects($projects)
            ->create();

        $specialUser = User::factory()->create();
        $taskCount = 20;
        Task::factory($taskCount)
            ->withOwner($specialUser)
            ->withOneOfGivenProjects($projects)
            ->create();

        $admin = User::factory()
            ->create(['admin' => true]);
        $this->readTasks(user: $admin, query: ['filter' => ['users' => [$specialUser->id]]])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($taskCount, 'data')
        ;
    }


    /**
     * Filter query test to test an admin could filter all tasks for one project
     *
     * @return void
     */
    public function test_listTasksOfASingleProject():void
    {
        $users      = User::factory(20)->create();
        $projects = Project::factory(10)->create();
        Task::factory(100)
            ->withOneOfGivenOwner($users)
            ->withOneOfGivenProjects($projects)
            ->create();

        $specialProject = Project::factory()->create();
        $taskCount = 23;
        Task::factory($taskCount)
            ->withOneOfGivenOwner($users)
            ->withProject($specialProject)
            ->create();

        $admin = User::factory()
            ->create(['admin' => true]);
        $this->readTasks(user: $admin, query: ['filter' => ['projects' => [$specialProject->id]]])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($taskCount, 'data')
        ;
    }


    /**
     * Filter query test to test an admin could list all overdue tasks
     *
     * @return void
     */
    public function test_listTasksWhereOverdue():void
    {
        $users      = User::factory(20)->create();
        $projects = Project::factory(10)->create();
        Task::factory(100)
            ->withOneOfGivenOwner($users)
            ->withOneOfGivenProjects($projects)
            ->withRandomDeadline(startDate: '+30 minutes')
            ->create(['status' => TaskStatusEnum::DONE])
            ;

        $taskCount = 2;
        Task::factory($taskCount)
            ->withOneOfGivenOwner($users)
            ->withOneOfGivenProjects($projects)
            ->withRandomDeadline(endDate: '-1 minute')
            ->create(['status' => TaskStatusEnum::IN_PROGRESS]);

        $admin = User::factory()
            ->create(['admin' => true]);

        $this->readTasks(user: $admin, query: ['filter' => ['overdue' => true]])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($taskCount, 'data')
        ;
    }

    /* -------------------------------------------------------------------------------------------------------------- */

    private function readTasks(?Task $task = null, ?User $user = null, ?array $query = null):TestResponse
    {
        $user = $user ?? User::factory()->create();
        Sanctum::actingAs($user);

        $url =  ($task)
            ? sprintf('/api/admin/tasks/%s', $task->id)
            : '/api/admin/tasks'
        ;

        if ($query) {
            $url = url()
                ->query($url, $query)
            ;
        }
        print $url.PHP_EOL;
        return $this->getJson($url);
    }
}
