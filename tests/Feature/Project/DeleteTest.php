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

    public function test_deleteProject():void
    {
        $project = Project::factory()->create();
        $this->deleteProjectsAsAuthenticatedUser($project)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('success', true)
            ;

    }

    public function test_couldNotDeleteProjectAsUnauthenticatedUser():void
    {
        $project = Project::factory()->create();
        $this->deleteJson('api/projects/'.$project->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ;
    }


    /* -------------------------------------------------------------------------------------------------------------- */

    private function deleteProjectsAsAuthenticatedUser(Project $project): TestResponse
    {
        $user = \App\Models\User::factory()->create();
        Sanctum::actingAs($user);
        return $this->deleteJson('api/projects/'.$project->id);
    }
}
