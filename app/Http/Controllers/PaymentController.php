<?php

namespace App\Http\Controllers;

use App\Book;
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
}
