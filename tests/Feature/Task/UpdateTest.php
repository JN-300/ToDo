<?php

namespace Tests\Feature\Task;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TaskTestsAbstract
{
    /**
     * Testing an authenticated user can update a task
     * - HTTP Status should be 200
     * - Response should be an array with the updated  task data (title, description, status, ...).
     *
     * @return void
     */
    public function test_updateTask(): void
    {

        $task = Task::factory()->create();

        $newData = [
            'title' => 'My new title',
            'description' => 'My new description',
            'status' => TaskStatusEnum::IN_PROGRESS->value
        ];

        $response = $this->updateTask($task, $newData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', $newData['title'])
            ->assertJsonPath('data.description', $newData['description'])
            ->assertJsonPath('data.status', $newData['status'])
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertEquals($newData['title'], $updatedTask->title);
        $this->assertEquals($newData['description'], $updatedTask->description);
        $this->assertEquals($newData['status'], $updatedTask->status);
    }

    /**
     * Testing an unauthenticated user cannot update a task
     *
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     * - task data should not be changed
     *
     * @return void
     */
    public function test_couldNotUpdateTaskAsUnauthenticatedUser(): void
    {
        $task = Task::factory()->create();

        $newData = [
            'title' => 'My new title',
            'description' => 'My new description',
            'status' => TaskStatusEnum::IN_PROGRESS->value
        ];

        $response =  $this->patchJson('/api/tasks/'.$task->id, $newData);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.')
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        // check if model is not modified
        $this->assertEquals($task->title, $updatedTask->title);
        $this->assertEquals($task->description, $updatedTask->description);
        $this->assertEquals($task->status, $updatedTask->status);

    }


    /** ----- SINGLE FIELD UPDATES -----  */

    /** ------ TITLE FIELD VALIDATIONS -------------- */

    /**
     * Testing an authenticated user can only change one field
     * - HTTP Status should be 200
     *
     * @return void
     */
    public function test_updateOnlyTitleFromTask(): void
    {
        $task       = Task::factory()->create();
        $newData    = ['title' => 'My new title'];

        $response = $this->updateTask($task, $newData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $newData['title'])
            ->assertJsonPath('data.description', $task->description)
            ->assertJsonPath('data.status', $task->status)
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertEquals($newData['title'], $updatedTask->title);

    }

    /**
     * Testing than an authenticate user cannot update a task with a title longer than 255 chars
     * - HTTP Status should be 422
     * - Response should include a corresponding error message telling about the error in the title field validation
     * @return void
     */
    public function test_couldNotUpdateTaskWithATitleLongerThan255Chars()
    {
        $task = Task::factory()->create();

        // create a random example text with min 256 chars
        $strLength = 256;
        $exampleText = fake()->text();
        while (($currentStrLength = strlen($exampleText)) < $strLength)
        {
            $exampleText .= fake()->text();
        }
        // test for char length
        $this->assertTrue(strlen($exampleText) >= $strLength);
        $newData = [
            'title' => $exampleText
        ];

        $response = $this->updateTask($task, $newData);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.title', fn(mixed $value) => is_array($value))
            ->assertJsonPath('errors.title.0',  'The title field must not be greater than 255 characters.')
        ;
    }


    /**
     * Testing an authenticated user can not update a field with an empty string
     * - HTTP Status should be 422
     *
     * @return void
     */
    public function test_couldNotUpdateTaskWithEmptyTitle(): void
    {
        $task       = Task::factory()->create();
        $newData    = ['title' => ''];

        $response = $this->updateTask($task, $newData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.title', fn(mixed $value) => is_array($value))
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertNotEquals($newData['title'], $updatedTask->title);
    }


    /** ------ DESCRIPTION FIELD VALIDATIONS -------------- */

    /**
     * Testing an authenticated user can only change one field
     * - HTTP Status should be 200
     *
     * @return void
     */
    public function test_updateOnlyDescriptionFromTask(): void
    {
        $task       = Task::factory()->create();
        $newData    = ['description' => 'My new description'];

        $response = $this->updateTask($task, $newData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $task->title)
            ->assertJsonPath('data.description', $newData['description'])
            ->assertJsonPath('data.status', $task->status)
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertEquals($newData['description'], $updatedTask->description);
    }



    /**
     * Testing an authenticated user can not update a field with an empty string
     * - HTTP Status should be 422
     *
     * @return void
     */
    public function test_couldNotUpdateTaskWithEmptyDescription(): void
    {
        $task       = Task::factory()->create();
        $newData    = ['description' => ''];

        $response = $this->updateTask($task, $newData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.description', fn(mixed $value) => is_array($value))
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertNotEquals($newData['description'], $updatedTask->description);
    }



    /** ------ STATUS FIELD VALIDATIONS -------------- */

    /**
     * Testing an authenticated user can only change one field
     * - HTTP Status should be 200
     *
     * @return void
     */
    public function test_updateOnlyStatusFromTask(): void
    {
        $task       = Task::factory()->create();
        $newData    = [ 'status' => TaskStatusEnum::IN_PROGRESS->value];
        $response   = $this->updateTask($task, $newData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $task->title)
            ->assertJsonPath('data.description', $task->description)
            ->assertJsonPath('data.status', $newData['status'])
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertEquals($newData['status'], $updatedTask->status);
    }

    /**
     * Testing that an authenticated user cannot update a task with a wrong status
     * - HTTP Status should be 422
     * - Response should include a corresponding error message telling about the error in the title field validation
     *
     * @return void
     */
    public function test_couldNotUpdateStatusFromTaskWithWrongValue()
    {
        $task       = Task::factory()->create();
        $newData    = [ 'status' => 'Wrong Status Value'];
        $response   = $this->updateTask($task, $newData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.status', fn(mixed $value) => is_array($value))
            ->assertJsonPath('errors.status.0',  'The selected status is invalid.')
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertNotEquals($newData['status'], $task->status);
    }

    /**
     * Testing an authenticated user can not update a field with an empty string
     * - HTTP Status should be 422
     *
     * @return void
     */
    public function test_couldNotUpdateTaskWithEmptyStatus(): void
    {
        $task = Task::factory()
            ->create([
                'deadline' => Carbon::now()->modify('+ 30minutes')
            ]);
        $newData = [ 'status' => ''];
        $response = $this->updateTask($task, $newData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.status', fn(mixed $value) => is_array($value))
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertNotEquals($newData['status'], $updatedTask->status);
    }


    /** ------ DEADLINE  FIELD VALIDATIONS -------------- */

    /**
     * Teastin an authenticate user can update the deadline of a task
     * - HTTP Status should be 200
     * - Task deadline has to be set with the new dateTime
     *
     * @return void
     */
    public function test_updateDeadlineOfTask(): void
    {
        $task = Task::factory()->create();
        $newData = [
            'deadline' => $task->deadline
                ->modify('+30 days')
                ->format('Y-m-d H:i:s')
        ];
        $response = $this->updateTask($task, $newData);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.deadline', $newData['deadline'])
        ;
        $updatedTask = Task::find($task)->first();
        $this->assertEquals($updatedTask->deadline, $newData['deadline']);
        $this->assertNotEquals($updatedTask->deadline, $task->deadline);
    }


    /**
     * Testing that an authenticate user cannot update a task with a deadline lower than now
     * - HTTP Status should be 422
     * - Response should include a corresponding error message telling about the error in the deadline field validation
     *
     * @return void
     */
    public function test_couldNotUpdateTaskWithADeadlineLowerThanNow():void
    {

        $task = Task::factory()
            ->create([
                'deadline' => Carbon::now()->modify('+5 days')
            ]);

        $newDeadline = Carbon::now()->modify('-5 seconds')
            ->format('Y-m-d H:i:s')
        ;

        $newData = [ 'deadline' => $newDeadline];
        $response = $this->updateTask($task, $newData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.deadline', fn(mixed $value) => is_array($value))
        ;
        // reload Task from DB
        $updatedTask = Task::find($task)->first();
        $this->assertEquals($task->deadline, $updatedTask->deadline);
        $this->assertNotEquals($newData['deadline'], $updatedTask->deadline);
    }


    /* -------------------------------------------------------------------------------------------------------------- */


    /**
     * @param Task $task
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    private function updateTask(Task $task, array $data):TestResponse
    {

        $user = User::all()->first();
        Sanctum::actingAs($user);
        return $this->patchJson('/api/tasks/'.$task->id, $data);
    }
}
