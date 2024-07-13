<?php

namespace Tests\Feature\Project;

use App\Enums\TaskStatusEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTest extends TestCase
{

    use RefreshDatabase;
    /**
     * Testing to create a task as an authenticated user
     * - HTTP Status should be 201
     * - response should have sucess: true,
     * - response should have an array with main key data and the submitted title, description and status values
     *
     * @return void
     */
    public function test_createProject(): void
    {
        $data = [
            'title' => 'my first project'
        ];

        $response  = $this->createProjectAsAuthUser($data);

        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', $data['title'])
        ;
    }



    /**
     * Testing that an unautheticated user cannot create a project
     * - HTTP Status should be 401
     * - Response should be message: Unauthenticated.
     *
     * @return void
     */
    public function test_couldNotCreateProjectAsUnautheticatedUser():void
    {
        $data = [
            'title' => 'my first project'
        ];

        $response = $this->postJson('/api/projects/', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.')
        ;

    }


    /**
     * Testing that an authenticated user cannot create a project without a title
     * - HTTP Status should be 422
     * - Response should have an array with main key errors and subarray with corresponding error messages for field title
     *
     * @return void
     */
    public function test_couldNotCreateProjectWithoutATitle():void
    {
        $data = [];
        $response  = $this->createProjectAsAuthUser($data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.title', fn(mixed $value) => is_array($value))
        ;
    }


    /**
     * Testing that an authenticated user cannot create a project with an empty title
     * - HTTP Status should be 422
     * - Response should have an array with main key errors and subarray with corresponding error messages for field title
     *
     * @return void
     */
    public function test_couldNotCreateProjectWithAnEmptyTitle():void
    {
        $data = [
            'title' => ''
        ];
        $response  = $this->createProjectAsAuthUser($data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.title', fn(mixed $value) => is_array($value))
        ;
    }

    /* -------------------------------------------------------------------------------------------------------------- */

    /**
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    private function createProjectAsAuthUser(array $data): TestResponse
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $this->postJson('/api/projects/', $data);
    }
}
