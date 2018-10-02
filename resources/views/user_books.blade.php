@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($books as $book)
                    <div class="card m-3">
                        <div class="card-header">{{ $book->title }}</div>

                        <div class="card-body">
                            {{ $book->description }}
                        </div>
                        <div class="card-footer">
                            <a href="#" class="btn btn-info btn-sm">Refund</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

