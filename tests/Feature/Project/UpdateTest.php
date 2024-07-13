<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_updateProject():void
    {
        $project = Project::factory()->create();
        $newData = [
            'title' => 'My first project'
        ];
        $this->updateProjectsAsAuthenticatedUser($project, $newData)->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', $newData['title'])
            ;

    }


    public function test_couldNotUpdateProjectAsUnauthenticatdUser():void
    {
        $project = Project::factory()->create();
        $newData = [
            'title' => 'My first project'
        ];
        $this->patchJson('api/projects/'.$project->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.')
        ;
    }

    public function test_couldNotUpdateProjectWithEmptyTitle():void
    {
        $project = Project::factory()->create();
        $newData = [
            'title' => ''
        ];
        $this->updateProjectsAsAuthenticatedUser($project, $newData)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.title', fn(mixed $value) => is_array($value))
            ;
    }

    /* -------------------------------------------------------------------------------------------------------------- */

    private function updateProjectsAsAuthenticatedUser(Project $project, array $updateData): TestResponse
    {
        $user = \App\Models\User::factory()->create();
        Sanctum::actingAs($user);
        return $this->patchJson('api/projects/'.$project->id, $updateData);
    }
}
