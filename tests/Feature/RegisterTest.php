<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function registration_form()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /** @test */
    public function a_user_can_register()
    {
        $user = factory(User::class)->make();

        $response = $this->post('register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(302);
        $this->assertTrue($this->isAuthenticated());
    }

    /** @test */
    public function invalid_user_can_not_be_register()
    {
        $user = factory(User::class)->make();

        $response = $this->post('register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'invalid'
        ]);

        $response->assertSessionHasErrors();
        $this->assertFalse($this->isAuthenticated());
    }
}
