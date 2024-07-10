<?php

namespace Tests\Feature\Task;

use App\Enums\TaskStatusEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Psy\Util\Str;
use Tests\TestCase;

class CreateTaskTest extends TaskTestsAbstract
{
    /**
     * Testing to create a task as an authenticated user
     * - HTTP Status should be 201
     * - response should have sucess: true,
     * - response should have an array with main key data and the submitted title, description and status values
     *
     * @return void
     */
    public function test_createTask(): void
    {
        $data = [
            'title' => 'my task',
            'description' => 'my task description',
            'status' => TaskStatusEnum::TODO->value,
        ];

        $response = $this->createTask($data);


        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', $data['title'])
            ->assertJsonPath('data.description', $data['description'])
            ->assertJsonPath('data.status', $data['status'])
        ;
    }

    /**
     * Testing that an unautheticated user cannot create a task
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     *
     * @return void
     */
    public function test_couldNotCreateTaskAsUnautheticatedUser():void
    {
        $data = [
            'title' => 'my task',
            'description' => 'my task description',
            'status' => TaskStatusEnum::TODO->value
        ];

        $response = $this->postJson('/api/tasks/', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.')
        ;

    }

    /**
     * Testing than an authenticate user cannot create a task with a title longer than 255 chars
     * - HTTP Status should be 422
     * - Response should include a corresponding error message telling about the error in the title field validation
     * @return void
     */
    public function test_couldNotCreateTaskWithATitleLongerThan255Chars()
    {

        // create an random example text with min 256 chars
        $strLength = 256;
        $exampleText = fake()->text();
        while (($currentStrLength = strlen($exampleText)) < $strLength)
        {
            $exampleText .= fake()->text();
        }

        // test for char length
        $this->assertTrue(strlen($exampleText) >= $strLength);
        $data = [
            'title' => $exampleText,
            'description' => 'my task description',
            'status' => 'done'
        ];

        $response = $this->createTask($data);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.title', fn(mixed $value) => is_array($value))
            ->assertJsonPath('errors.title.0',  'The title field must not be greater than 255 characters.')
        ;
    }

    /**
     * Testing that an authenticated user cannot create a task with a wrong status
     * - HTTP Status should be 422
     * - Response should include a corresponding error message telling about the error in the title field validation
     *
     * @return void
     */
    public function test_couldNotCreateTaskWithWrongStatus()
    {
        $data = [
            'title' => 'My task title',
            'description' => 'my task description',
            'status' => 'this is a wrong status'
        ];

        $response = $this->createTask($data);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.status', fn(mixed $value) => is_array($value))
            ->assertJsonPath('errors.status.0',  'The selected status is invalid.')
        ;
    }

    /**
     * Testing an authenticated user cannot create a task without a title
     * - HTTP Status should be 422
     * - Response should have an array with main key errors and subarray with corresponding error messages for field title
     *
     * @return void
     */
    public function test_couldNotCreateTaskWithoutTitle():void
    {
        $data = [
            'title' => '',
            'description' => 'my task description',
            'status' => 'done'
        ];
        $response = $this->createTask($data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.title', fn(mixed $value) => is_array($value))
        ;
    }

    /**
     * Testing an authenticated user cannot create a task without a description
     * - HTTP Status should be 422
     * - Response should have an array with main key errors and subarray with corresponding error messages for field description
     *
     * @return void
     */
    public function test_couldNotCreateTaskWithoutDescription():void
    {
        $data = [
            'title' => 'my title',
            'description' => '',
            'status' => 'done'
        ];
        $response = $this->createTask($data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.description', fn(mixed $value) => is_array($value))
        ;
    }


    /**
     * Testing an authenticated user cannot create a task without a status
     * - HTTP Status should be 422
     * - Response should have an array with main key errors and subarray with corresponding error messages for field status
     *
     * @return void
     */
    public function test_couldNotCreateTaskWithoutStatus():void
    {
        $data = [
            'title' => 'My title',
            'description' => 'my task description',
            'status' => ''
        ];
        $response = $this->createTask($data);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.status', fn(mixed $value) => is_array($value))
        ;
    }


    /* -------------------------------------------------------------------------------------------------------------- */


    /**
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    private function createTask(array $data): TestResponse
    {
        $user = User::all()->first();
        Sanctum::actingAs($user);
        return $this->postJson('/api/tasks/', $data);
    }
}