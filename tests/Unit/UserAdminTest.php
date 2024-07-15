<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdminTest extends TestCase
{

    use RefreshDatabase;
    public function test_createAnUserWithAdminStatus():void
    {
        $user = User::factory()
            ->make([
                'admin' => true
            ]);

        $this->assertTrue($user->isAdmin());
    }
}
