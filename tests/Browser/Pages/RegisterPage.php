<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class RegisterPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/register';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@name'      => 'input[name=name]',
            '@email'     => 'input[name=email]',
            '@password'  => 'input[name=password]',
            '@conf_pass' => 'input[name=password_confirmation]',
            '@register'  => 'button[type=submit]',
        ];
    }
}
