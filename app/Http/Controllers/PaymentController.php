<?php

namespace App\Http\Controllers;

use App\Book;

use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function paymentForm($id)
    {
        $book = Book::findOrFail($id);

        return view('payment_form', [
            'book' => $book
        ]);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'card_no' => 'required',
            'cvvNumber' => 'required | digits:3',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required'
        ]);

        $data = $request->except('_token');
        $stripe = Stripe::make(env('STRIPE_KEY'));
        try {
            $token = $stripe->tokens()->create([
                'card' => [
                    'number' => $data['card_no'],
                    'exp_month' => $data['ccExpiryMonth'],
                    'exp_year' => $data['ccExpiryYear'],
                    'cvc' => $data['cvvNumber'],
                ],
            ]);

            if (!isset($token['id'])) {
                return redirect()->back();
            }
            $stripeCharge = Stripe::make(env('STRIPE_SECRET'));
            $charge = $stripeCharge->charges()->create([
                'card' => $token['id'],
                'currency' => 'USD',
                'amount' => $data['amount'],
                'description' => 'Book paid',
            ]);

            if ($charge['status'] == 'succeeded') {
                return redirect()->route('welcome')->with('message', 'Book paid successfully');
            } else {
                return redirect()->route('welcome')->with('message', 'Payment error');
            }

        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('message', $e->getMessage());
        }
    }
}
