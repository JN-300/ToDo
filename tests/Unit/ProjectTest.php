<?php

namespace Tests\Unit;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProjectTest extends TestCase
{

    use RefreshDatabase;

    public function test_factoryCreate():void
    {
        $project = Project::factory()->create();
        $this->assertTrue($project instanceof Project);
        $this->assertTrue(Str::isUuid($project->id));
//        $this->assertNotEmpty($project->title);
    }

    public function test_createProject():void
    {
        $projectData = [
            'title' => 'Ich bin der Titel des Testprojektes'
        ];

        $project =Project::create($projectData);
        $this->assertTrue($project instanceof Project);;
    }
    public function test_readProject():void
    {

        $project = Project::factory()->create([
            'title' => 'My project'
        ]);

        $readProject = Project::find($project)->first();
        $this->assertEquals($project->title, $readProject->title);
    }
    public function test_updateProject(): void
    {
        $project = Project::factory()->create();
        $newData = [
            'title' => 'my updated title'
        ];
        $project->update($newData);
        $updatedProject = Project::find($project)->first();
        $this->assertEquals($project->title, $updatedProject->title);
    }

    public function test_deleteProject(): void
    {
        $project = Project::factory()->create();
        $this->assertDatabaseHas('projects', ['id' => $project->id]);

        $project->delete();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
