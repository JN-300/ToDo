<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing route for deleting a project
     * Currently every authenticated user can delete every project
     * - HTTP Status should be 200
     *
     * @return void
     */
    public function test_deleteProject():void
    {
        $project = Project::factory()->create();
        $this->deleteProjectsAsAuthenticatedUser($project)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('success', true)
            ;

    }

    /**
     * Testing that an unauthenticated user cannot delete a project
     *
     * HTTP Status should be 401
     * @return void
     */
    public function test_couldNotDeleteProjectAsUnauthenticatedUser():void
    {
        $project = Project::factory()->create();
        $this->deleteJson('api/projects/'.$project->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ;
    }


    /* -------------------------------------------------------------------------------------------------------------- */

    /**
     * Helper method to call route
     * @param Project $project
     * @return TestResponse
     */
    private function deleteProjectsAsAuthenticatedUser(Project $project): TestResponse
    {
        $user = \App\Models\User::factory()->create();
        Sanctum::actingAs($user);
        return $this->deleteJson('api/projects/'.$project->id);
    }
}
