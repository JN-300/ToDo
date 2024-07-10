<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private ?User $user = null;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    /**
     * Test to check for correct http status and filled error message for a login attempt without a password
     * @return void
     */
    public function test_couldNotLoginWithoutPassword():void
    {

        $response = $this->postJson(
            '/api/token',
            [
                'email' => 'test@test.example'
            ]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonPath('errors.password', fn(mixed $value) => is_array($value))
        ;
    }

    /**
     * Test to check for correct http status and filled error message for a login attempt with a wrong password
     * @return void
     */
    public function test_couldNotLoginWithWrongPassword():void
    {
        $response = $this->postJson(
            '/api/token',
            [
                'email' => 'test@test.example',
                'password' => 'WrongPassword'
            ]
        );

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.email', fn(mixed $value) => is_array($value))
            ;

    }

    /**
     * Test to check correct http status and returning of a generated access token after login attempt with right email and password
     * @return void
     */
    public function test_couldLoginWithRightCredentials(): void
    {
        $response = $this->postJson(
            '/api/token',
            [
                'email' => 'test@test.example',
                'password' => 'myPass123'
            ]
        );
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('access_token', fn(mixed $value) => is_string($value))
        ;
    }

    /**
     * Test to check for correct http status after attempt to receive an user detail with wrong bearer token
     * @return void
     */
    public function test_couldNotShowUserDetailsWithWrongToken(): void
    {
        $response = $this->getJson('/api/user',
            [
                'Authorization' => 'Bearer WrongToken'
            ]
        );
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.')
        ;
    }

    /**
     * Test to check for correct http status after attempt to receive an user detail with right bearer token
     * @return void
     */
    public function test_couldShowUserDetailsWithGenerateToken(): void
    {
        $response = $this->postJson(
            '/api/token',
            [
                'email' => 'test@test.example',
                'password' => 'myPass123'
            ]
        );
        $token = json_decode($response->getContent())->access_token;

        $response = $this->getJson('/api/user',
            [
                'Authorization' => 'Bearer '.$token
            ]
        );
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('name', $this->user->name);
    }


    /**
     * Test logout method
     * @return void
     */
    public function test_couldDeleteToken(): void
    {
        $response = $this->postJson(
            '/api/token',
            [
                'email' => 'test@test.example',
                'password' => 'myPass123'
            ]
        );
        $token = json_decode($response->getContent())->access_token;

        $response = $this->deleteJson('/api/token',
            [
                'Authorization' => 'Bearer '.$token
            ]
        );
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonPath('message', 'Unauthenticated.')
            ;

    }


    /** ------------------------------------------------------------------------------------------------------------- */

    /**
     * Helper method to generate an user
     * @return User
     */
    private function createUser():User
    {
        $user = User::factory()->create([
            'email' => 'test@test.example',
            'password' => Hash::make('myPass123')
        ]);
        return $user;
    }


}
