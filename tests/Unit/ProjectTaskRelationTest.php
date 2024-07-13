<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psy\Util\Str;
use Tests\TestCase;

class ProjectTaskRelationTest extends TestCase
{
    use RefreshDatabase;
    public function test_createTaskWithProjectRelation():void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id ]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => $project->id]);
        $this->assertEquals($task->project->id, $project->id);
    }

    public function test_couldNotCreateTaskWithWrongProjectRelation():void
    {
        $this->expectException(QueryException::class);
        $task = Task::factory()->create([ 'project_id' => fake()->uuid]);
    }

    public function test_projectIdOfTaskIsNullAfterDeleteProject():void
    {

        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $project->delete();
        $task->refresh();
        $this->assertNull($task->project);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => null]);
    }

    public function test_updateProjectTaskRelation():void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $newProject = Project::factory()->create();

        $task->update([
            'project_id' => $newProject->id
        ]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => $newProject->id]);

    }

    public function test_removeProjectTaskRelation():void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $task->update(['project_id' => null]);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => null]);
    }

    public function test_listAllTasksFromProject():void
    {
        $projectOne = Project::factory()->create();
        $projectTwo = Project::factory()->create();

        $tasksOne = Task::factory(5)->create(['project_id'=>$projectOne]);
        $taskTwo  = Task::factory(15)->create(['project_id'=>$projectTwo]);

        $this->assertCount(5, $projectOne->tasks);
        $this->assertCount(15, $projectTwo->tasks);

    }


}
