<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;


class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function login_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                    ->assertSee('Login');
        });
    }

    /** @test */
    public function credentials_need_to_be_matched()
    {
        factory(User::class)->create();

        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)
                ->type('@email', 'test123@gmail.com')
                ->type('@password', 'secret123')
                ->press('@login')
                ->assertSee('These credentials do not match our records.');
        });
    }

    /** @test */
    public function user_login()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
           $browser->visit(new LoginPage)
                   ->type('@email', $user->email)
                   ->type('@password', 'secret')
                   ->press('@login')
                   ->assertPathIs('/home');
        });
    }
}
