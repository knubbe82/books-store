<?php

namespace Tests\Browser\Pages;

use App\Book;
use Laravel\Dusk\Browser;

class PaymentPage extends Page
{
    protected $book;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/payment/' . $this->book->id;
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
            '@card'    => 'input[name=card_no]',
            '@cvv'     => 'input[name=cvvNumber]',
            '@month'   => 'input[name=ccExpiryMonth]',
            '@year'    => 'input[name=ccExpiryYear]',
            '@pay'     => 'button[type=submit]',
        ];
    }
}
