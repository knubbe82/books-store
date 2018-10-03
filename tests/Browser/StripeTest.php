<?php

namespace Tests\Browser;

use App\Book;
use App\User;
use Cartalyst\Stripe\Stripe;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Pages\LoginPage;
use Tests\Browser\Pages\MyBooksPage;
use Tests\Browser\Pages\PaymentPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StripeTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function non_auth_user_can_not_see_buy_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/home')
                ->assertDontSee('Buy')
                ->assertPathIs('/login');
        });
    }

    /** @test */
    public function auth_user_can_see_buy_button()
    {
        $user = factory(User::class)->create();
        factory(Book::class)->create();
        $this->be($user);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new LoginPage)
                ->loginAs($user)
                ->visit(new HomePage)
                ->assertSee('Buy');
        });
    }

    /** @test */
    public function auth_user_can_get_payment_form()
    {
        $user = factory(User::class)->create();
        $book = factory(Book::class)->create();

        $this->browse(function (Browser $browser) use ($user, $book) {
            $browser->visit(new HomePage)
                ->click('@buy')
                ->on(new PaymentPage($book))
                ->assertSee('Card Number');
        });
    }

    /** @test */
    public function auth_user_can_buy_a_book_that_can_be_refunded()
    {
        $user = factory(User::class)->create();
        $book = factory(Book::class)->create();

        $this->browse(function (Browser $browser) use ($user, $book) {
            $browser->visit(new HomePage)
                ->click('@buy')
                ->on(new PaymentPage($book))
                ->type('@card', '4242424242424242')
                ->type('@cvv', '123')
                ->type('@month', '02')
                ->type('@year', '2025')
                ->press('@pay')
                ->assertPathIs('/')
                ->assertSee('Book paid successfully')
                ->visit(new MyBooksPage)
                ->assertSee('Refund')
                ->on(new MyBooksPage)
                ->press('@refund')
                ->assertSee('Refunded successfully');
        });
    }
}
