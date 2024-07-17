<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psy\Util\Str;
use Tests\TestCase;

class ProjectTaskRelationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing create a task with a project relation
     * @return void
     */
    public function test_createTaskWithProjectRelation():void
    {
        $owner      = User::factory()->create();
        $project    = Project::factory()->create();
        $task       = Task::factory()
            ->withOwner($owner)
            ->create(['project_id' => $project->id ]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => $project->id]);
        $this->assertEquals($task->project->id, $project->id);
    }

    /**
     * Testing that a task could not create with a not existing project relation
     *
     * @return void
     */
    public function test_couldNotCreateTaskWithWrongProjectRelation():void
    {
        $this->expectException(QueryException::class);

        $owner      = User::factory()->create();
        $task = Task::factory()
            ->withOwner($owner)
            ->create([ 'project_id' => fake()->uuid]);
    }

    /**
     * Testing that the project relation of a task is removed after deleting the project
     * @return void
     */
    public function test_projectIdOfTaskIsNullAfterDeleteProject():void
    {
        $owner      = User::factory()->create();
        $project    = Project::factory()->create();
        $task       = Task::factory()
            ->withOwner($owner)
            ->create(['project_id' => $project->id]);
        $project->delete();
        $task->refresh();
        $this->assertNull($task->project);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => null]);
    }

    /**
     * Testing updating a project relation
     * @return void
     */
    public function test_updateProjectTaskRelation():void
    {
        $owner      = User::factory()->create();
        $project    = Project::factory()->create();
        $task       = Task::factory()
            ->withOwner($owner)
            ->create(['project_id' => $project->id]);
        $newProject = Project::factory()->create();

        $task->update([
            'project_id' => $newProject->id
        ]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => $newProject->id]);

    }

    /**
     * Testing removing a project relation
     * @return void
     */
    public function test_removeProjectTaskRelation():void
    {
        $owner      = User::factory()->create();
        $project    = Project::factory()->create();
        $task       = Task::factory()
            ->withOwner($owner)
            ->create(['project_id' => $project->id]);

        $task->update(['project_id' => null]);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => null]);
    }

    /**
     * Testing scope for task listing through a project
     *
     * @return void
     */
    public function test_listAllTasksFromProject():void
    {
        $owner      = User::factory()->create();
        $projectOne = Project::factory()->create();
        $projectTwo = Project::factory()->create();

        $tasksOne = Task::factory(5)
            ->withOwner($owner)
            ->create(['project_id'=>$projectOne]);
        $taskTwo  = Task::factory(15)
            ->withOwner($owner)
            ->create(['project_id'=>$projectTwo]);

        // opposite test
        $testUser = User::factory()->create();
//        $this->actingAs(User::factory()->create());
        // reset project data
        $this->assertCount(0, $projectOne->tasks()->forUser($testUser)->get());
        $this->assertCount(0, $projectTwo->tasks()->forUser($testUser)->get());

        // user test
//        $this->actingAs($owner);
        // reset project data
        $this->assertCount(5, $projectOne->tasks()->forUser($owner)->get());
        $this->assertCount(15, $projectTwo->tasks()->forUser($owner)->get());
    }


}
