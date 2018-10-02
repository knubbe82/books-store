<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Books Store</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/books.css') }}" rel="stylesheet">
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                {{--<a href="{{ url('/home') }}">Home</a>--}}
                <a href="{{ route('user.books') }}">My books</a>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    @endif
    @if (session('message'))
        <div class="alert alert-{{ session('alert') }}">
            {{ session('message') }}
        </div>
    @endif

    <div class="content">
        <div class="title m-b-md">
            Books Store
        </div>

        @foreach ($books as $book)
            <div class="card">
                <div class="card-header">
                    {{ $book->title }}
                </div>
                <div class="card-body">{{ $book->description }}</div>
                <div class="card-footer">
                    {{ $book->price }} $
                    @auth
                        <a href="{{ route('payment.form', ['id' => $book->id]) }}">Buy</a>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
</div>
</body>
</html>
