<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function userBooks()
    {
        $books = auth()->user()->books()->get();

        return view('user_books', [
           'books' => $books
        ]);
    }
}
