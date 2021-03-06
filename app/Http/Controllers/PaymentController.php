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
        try {
            $token = $this->token($data);

            if (!isset($token['id'])) {
                return redirect()->back();
            }

            $charge = $this->charge($token, $data);

            if ($charge['status'] == 'succeeded') {
                $this->successPayment($data, $charge);

                return redirect()->route('welcome')->with('message', 'Book paid successfully');
            } else {
                return redirect()->route('welcome')->with('message', 'Payment error');
            }

        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('message', $e->getMessage());
        }
    }

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

    private function successPayment($data, $charge)
    {
        $book = Book::findOrFail($data['book_id']);
        $book->users()->attach(\auth()->user()->id, ['charge' => $charge['id']]);
    }

    public function refund(Request $request)
    {
        $stripe = Stripe::make(env('STRIPE_SECRET'));
        $stripe->refunds()->create($request->charge, $request->price, [
            'reason' => 'requested_by_customer',
        ]);

        $book = Book::findOrFail($request->book);
        $book->users()->detach(\auth()->user()->id, ['charge' => $request->charge]);

        return redirect()->back()->with(['message' => 'Refunded successfully', 'alert' => 'info']);
    }
}
