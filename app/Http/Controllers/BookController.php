<?php

namespace App\Http\Controllers;

use App\Book;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    public function index()
    {
        $books = Book::all();

        return view('welcome', [
            'books' => $books
        ]);
    }

    public function userBooks()
    {
        $books = auth()->user()->books()->get();

        return view('user_books', [
           'books' => $books
        ]);
    }
}
