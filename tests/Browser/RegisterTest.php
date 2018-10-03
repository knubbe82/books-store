<?php

namespace Tests\Browser;

use Tests\Browser\Pages\RegisterPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function register_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->assertSee('Register');
        });
    }

    /** @test */
    public function user_register()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                ->type('@name', 'Nikola Zivkovic')
                ->type('@email', 'knubbe@nadlanu.com')
                ->type('@password', 'secret')
                ->type('@conf_pass', 'secret')
                ->press('@register')
                ->assertPathIs('/home');
        });
    }
}
