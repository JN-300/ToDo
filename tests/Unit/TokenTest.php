<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Psy\Util\Str;

class TokenTest extends TestCase
{

    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Testing token create message
     * @return void
     */
    public function test_returnTokenForExistingUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $token = $user->createToken('testtoken');
        $this->assertIsString($token->plainTextToken);
    }

    /**
     * Testing token delete method
     * @return void
     */
    public function test_deleteToken(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $token = $user->createToken('testtoken');
        $tokenId = $token->accessToken->id;
        $this->assertTrue($user->tokens()->where('id', $tokenId)->count() === 1);
        $user->tokens()->where('id', $tokenId)->delete();
        $this->assertTrue($user->tokens()->where('id', $tokenId)->count() === 0);
    }
}
