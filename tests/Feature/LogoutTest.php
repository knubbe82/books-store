<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function logout_auth_user()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->post('/logout');
        $response->assertStatus(302);
        $this->assertFalse($this->isAuthenticated());
    }
}
