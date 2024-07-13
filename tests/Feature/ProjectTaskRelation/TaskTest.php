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

    public function test_createdTaskHasProjectRelation():void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $response = $this->showTasksAsAuthenticateUser($task);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.project.id', $project->id)
            ;
    }
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

    public function test_listTasksReturnsOnlyProjectIds()
    {
        /** @var Collection $projects */
        $projects = Project::factory(4)->create();
        /** @var Collection $tasks */
        $tasks = Task::factory(10)->create([
            'project_id' => fake()->randomElement($projects->pluck('id'))
        ]);

        $response = $this->showTasksAsAuthenticateUser()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data')
            ->assertJsonMissingPath('data.0.project.id')
            ->assertJsonPath('data.0.project_id', fn(mixed $value) => \Illuminate\Support\Str::isUuid($value))
            ;
    }

    public function test_listTasksReturnsProjectDataIfDemanded()
    {
        /** @var Collection $projects */
        $projects = Project::factory(4)->create();
        /** @var Collection $tasks */
        $tasks = Task::factory(10)->create([
            'project_id' => fake()->randomElement($projects->pluck('id'))
        ]);

        $response = $this->showTasksAsAuthenticateUser(query: ['with' => 'project'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('data.0.project.id', fn(mixed $value) => \Illuminate\Support\Str::isUuid($value))
        ;
    }




    /* -------------------------------------------------------------------------------------------------------------- */


    /**
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    private function createTaskAsAuthenticatedUser(array $data): TestResponse
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $this->postJson('/api/tasks/', $data);
    }

    private function showTasksAsAuthenticateUser(?Task $task = null, ?array $query = null): TestResponse
    {
        $user = User::factory()->create();
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
