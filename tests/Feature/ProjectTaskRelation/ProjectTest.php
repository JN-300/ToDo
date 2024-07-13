<?php

namespace Tests\Feature\ProjectTaskRelation;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    /**
     * A basic feature test example.
     */
    public function test_listingTasksOfOneProject(): void
    {
        $project = Project::all()->first();
        $taskCount = $project->tasks->count();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->getJson('api/projects/'.$project->id.'/tasks')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($taskCount, 'data')
            ;
    }

    /**
     * A basic feature test example.
     */
    public function test_couldNotListingTasksOfOneProjectAsUnauthenticatedUser(): void
    {
        $project = Project::all()->first();
        $taskCount = $project->tasks->count();

        $this->getJson('api/projects/'.$project->id.'/tasks')
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ;
    }
}
