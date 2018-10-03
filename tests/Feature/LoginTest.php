<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class LoginTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function login_form()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /** @test */
    public function login_a_valid_user()
    {
        $user = factory(User::class)->create();
        $data = [
            'email' => $user->email,
            'password' => 'secret'
        ];

        $response = $this->post('/login', $data);

        $response->assertStatus(302);
        $this->assertCredentials($data);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function invalid_user_can_not_login()
    {
        $user = factory(User::class)->create();
        $data = [
            'email' => $user->email,
            'password' => 'invalid'
        ];

        $response = $this->post('/login', $data);

        $response->assertSessionHasErrors();
        $this->assertInvalidCredentials($data);
        $this->assertFalse($this->isAuthenticated());
    }
}
