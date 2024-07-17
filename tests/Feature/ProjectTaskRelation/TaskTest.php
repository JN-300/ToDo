<?php

namespace Tests\Feature\ProjectTaskRelation;

use App\Enums\TaskStatusEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Psy\Util\Str;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing that a project relation is stored to a task after creation
     *
     * - HTTP Status 200
     * @return void
     */
    public function test_createdTaskHasProjectRelation():void
    {
        $owner      = User::factory()->create();
        $project    = Project::factory()->create();
        $task       = Task::factory()
            ->withOwner($owner)
            ->withProject($project)
            ->create();
        $this->showTasksAsAuthenticateUser(task: $task, user: $owner)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.project_id', $project->id)
            ;
    }


    /**
     * Testing that a task can be create with a project relation through api call
     *
     * - HTTP Status: 201
     * @return void
     */
    public function test_createTaskWithProjectRelation():void
    {
        $project = Project::factory()->create();

        $data = [
            'title' => 'My task',
            'description' => 'My description',
            'deadline' => Carbon::now()->modify('+1 day')->format('Y-m-d H:i:s'),
            'status' => TaskStatusEnum::TODO,
            'project_id' => $project->id
        ];

        $this->createTaskAsAuthenticatedUser($data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.project.id', $project->id)
            ;
    }

    /**
     * Testing a normal listing of tasks holds only the project ids and not the full project data in response
     *
     * - HTTP Status: 200
     * - Response missing project object in every task object
     * - Response has project_id as key in every task object
     * @return void
     */
    public function test_listTasksReturnsOnlyProjectIds():void
    {
        $owner = User::factory()->create();
        /** @var Collection $projects */
        $projects = Project::factory(4)->create();
        /** @var Collection $tasks */
        Task::factory(10)
            ->withOwner($owner)
            ->withOneOfGivenProjects($projects)
            ->create()
        ;

        $this->showTasksAsAuthenticateUser(user: $owner)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data')
            ->assertJsonMissingPath('data.0.project.id')
            ->assertJsonPath('data.0.project_id', fn(mixed $value) => \Illuminate\Support\Str::isUuid($value))
            ;
    }


    public function test_showSingleTaskWithEnrichedProject():void
    {

        $owner      = User::factory()->create();
        $project    = Project::factory()->create();
        $task       = Task::factory()->withOwner($owner)->withProject($project)->create();
        $this->showTasksAsAuthenticateUser(task: $task, user: $owner, query: ['with' => 'project'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.project.id', $project->id)
        ;
    }

    /**
     * Testing route calling with query param with:project returns also full project data in response
     *
     * - HTTP Status: 200
     * - Response has project object in every task object
     * - Response has project_id as key in every task object
     *
     * @return void
     */
    public function test_listTasksReturnsProjectDataIfDemanded():void
    {
        $owner = User::factory()->create();
        /** @var Collection $projects */
        $projects = Project::factory(4)->create();
        /** @var Collection $tasks */
        $tasks = Task::factory(10)
            ->withOwner($owner)
            ->withOneOfGivenProjects($projects)
            ->create()
        ;

         $this->showTasksAsAuthenticateUser(query: ['with' => 'project'], user: $owner)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('data.0.project.id', fn(mixed $value) => \Illuminate\Support\Str::isUuid($value))
        ;
    }

    /**
     * Testing listing all task which belongs to a project
     * should be filter out all tasks which user does not own
     *
     * - HTTP Status: 200
     * - response should only hold the tasks generated for this user
     * @return void
     */
    public function test_listAllTasksByProject():void
    {
        $project    = Project::factory()->create();
        $user       = User::factory()->create();
        $otherUsers = User::factory(10)->create();


        Task::factory(7)
            ->withOwner($user)
            ->withProject($project)
            ->create();

        Task::factory(100)
            ->withOneOfGivenOwner($otherUsers)
            ->withProject($project)
            ->create();

        Sanctum::actingAs($user);
        $this->getJson('api/tasks/project/'.$project->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(7, 'data')
        ;
    }

    /**
     * Testing an unauthenticated user can not list any tasks
     *
     * - HTTP Status: 401
     *
     * @return void
     */
    public function test_couldNotListingTasksOfOneProjectAsUnauthenticatedUser(): void
    {
        $project    = Project::factory()->create();
        Task::factory(7)
            ->withOwner(User::factory()->create())
            ->withProject($project)
            ->create();

        $this->getJson('api/tasks/project/'.$project->id.'')
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ;
    }



    /* -------------------------------------------------------------------------------------------------------------- */


    /**
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    private function createTaskAsAuthenticatedUser(array $data, ?User $user = null): TestResponse
    {
        $user = $user ?? User::factory()->create();
        Sanctum::actingAs($user);
        return $this->postJson('/api/tasks/', $data);
    }

    /**
     * @param Task|null $task
     * @param array|null $query
     * @param User|null $user
     * @return TestResponse
     */
    private function showTasksAsAuthenticateUser(?Task $task = null, ?array $query = null, ?User $user = null): TestResponse
    {
        $user = $user ?? User::factory()->create();
        Sanctum::actingAs($user);
        $url =  ($task)
            ? sprintf('/api/tasks/%s', $task->id)
            : '/api/tasks'
            ;

        if ($query) {
            $url = url()
                ->query($url, $query)
            ;
        }
        return $this->getJson($url);
    }
}
