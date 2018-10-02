@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('message'))
            <div class="alert alert-{{ session('alert') }}">
                {{ session('message') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($books as $book)
                    <div class="card m-3">
                        <div class="card-header">{{ $book->title }}</div>

                        <div class="card-body">
                            {{ $book->description }}
                        </div>
                        <div class="card-footer">
                            <form action="{{ route('payment.refund') }}" method="POST">
                                @csrf
                                <input type="hidden" name="charge" value="{{ $book->pivot->charge }}">
                                <input type="hidden" name="price" value="{{ $book->price }}">
                                <input type="hidden" name="book" value="{{ $book->id }}">
                                <button type="submit" class="btn btn-info btn-sm">Refund</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

