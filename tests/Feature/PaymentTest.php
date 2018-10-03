<?php

namespace Tests\Feature;

use App\Book;
use App\User;
use Cartalyst\Stripe\Stripe;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class PaymentTest extends TestCase
{
    use DatabaseTransactions;

    private function token($data)
    {
        $stripe = Stripe::make(env('STRIPE_KEY'));

        $token = $stripe->tokens()->create([
            'card' => [
                'number' => $data['card_no'],
                'exp_month' => $data['ccExpiryMonth'],
                'exp_year' => $data['ccExpiryYear'],
                'cvc' => $data['cvvNumber'],
            ],
        ]);

        return $token;
    }

    private function charge($token, $data)
    {
        $stripe = Stripe::make(env('STRIPE_SECRET'));

        $charge = $stripe->charges()->create([
            'card' => $token['id'],
            'currency' => 'USD',
            'amount' => $data['amount'],
            'description' => 'Book paid',
        ]);

        return $charge;
    }

    private function buy_a_book()
    {
        $book = factory(Book::class)->create();

        $data = [
            'card_no' => '4242424242424242',
            'cvvNumber' => '123',
            'ccExpiryMonth' => '02',
            'ccExpiryYear' => '2025',
            'amount' => $book->price,
        ];

        return $data;
    }

    /** @test */
    public function auth_user_can_see_buy_button()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get('/home');
        $response->assertStatus(200);
        $response->assertSee('Buy');
    }

    /** @test */
    public function non_auth_user_can_not_see_buy_button()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('Buy');
    }

    /** @test */
    public function buy_form()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $book = factory(Book::class)->create();

        $response = $this->get('/payment/' . $book->id);
        $response->assertStatus(200);
    }

    /** @test */
    public function non_auth_user_can_get_buy_form()
    {
        $book = factory(Book::class)->create();

        $response = $this->get('/payment/' . $book->id);
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function auth_user_can_buy_a_book()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $data = $this->buy_a_book();
        $token = $this->token($data);
        $charge = $this->charge($token, $data);

        $this->assertTrue($charge['status'] == 'succeeded');
    }

    /** @test */
    public function auth_user_can_not_buy_a_book_with_wrong_credentials()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        try {
            $data = $this->buy_a_book();
            $data['card_no'] = '123';
            $token = $this->token($data);
            $charge = $this->charge($token, $data);
        } catch (\Exception $e) {
            $this->assertEquals('The card number is not a valid credit card number.', $e->getMessage());
        }
    }

    /** @test */
    public function charged_book_can_be_refunded()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $data = $this->buy_a_book();
        $token = $this->token($data);
        $charge = $this->charge($token, $data);

        $charge_id = $charge['id'];

        $stripe = Stripe::make(env('STRIPE_SECRET'));
        $refund = $stripe->refunds()->create($charge_id, $data['amount'], [
            'reason' => 'requested_by_customer',
        ]);

        $this->assertTrue($refund['status'] == 'succeeded');
    }
}
