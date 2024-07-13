<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReadTest extends TestCase
{

    use RefreshDatabase;
    /**
     * Testing of listing all available projects
     *
     * - HTTP Status should be 200
     * - Response should have an array with main key errors and subarray with corresponding error messages for field title
     *
     * @return void
     */
    public function test_listTasks():void
    {
        $projectAmount = 20;
        Project::factory($projectAmount)->create();

        $this->assertDatabaseCount('projects', $projectAmount);
        $response = $this->readProjectsAsAuthenticatedUser();
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data', fn(array $data) => count($data) === $projectAmount)
        ;
    }

    /**
     * Testing, that an unauthenticated user could not list any projects
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     * @return void
     */
    public function test_couldNotListTasksAsUnauthenticatedUser():void
    {
        $response = $this->getJson('api/projects');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.')
            ;
    }


    /**
     * Testing that an authenticated user can show a project
     * - HTTP Status should be 200
     * - Response should be an array with the relevant task data (title, description, status, ...).
     *
     * @return void
     */
    public function test_showTask():void
    {
        $project = Project::factory()->create();
        $this->assertNotEmpty($project->title);
        $response = $this->readProjectsAsAuthenticatedUser($project);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $project->title)
            ;
    }

    /**
     * Testing that an unauthenticated user could not show a project
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     *
     * @return void
     */
    public function test_couldNotShowTaskAsUnauthenticatedUser():void
    {
        $project = Project::factory()->create();
        $response = $this->getJson('api/projects/'.$project->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.')
            ;
    }

    /* -------------------------------------------------------------------------------------------------------------- */

    private function readProjectsAsAuthenticatedUser(?Project $project = null): TestResponse
    {
        $user = \App\Models\User::factory()->create();
        Sanctum::actingAs($user);

        return ($project)
            ? $this->getJson('api/projects/'.$project->id)
            : $this->getJson('api/projects')
            ;
    }
}
