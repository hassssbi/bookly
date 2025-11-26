@extends('layouts.app')

@section('title', 'Book Preview | Bookly')
@section('page_title', 'Book Preview')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('seller.books.index') }}">My Books</a></li>
    <li class="breadcrumb-item active">{{ $book->title }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body text-center">
                    @if ($book->cover_path)
                        <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="img-fluid"
                            style="max-height: 350px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 350px;">
                            <span class="text-muted">No cover image</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-2">{{ $book->title }}</h2>

                    <p class="text-muted mb-1">
                        Category: {{ $book->category->name ?? '-' }}
                    </p>

                    <h3 class="text-success mb-3">
                        RM {{ number_format($book->price, 2) }}
                    </h3>

                    <p class="mb-3">
                        @if ($book->stock > 0)
                            <span class="badge badge-success">In stock ({{ $book->stock }})</span>
                        @else
                            <span class="badge badge-danger">Out of stock</span>
                        @endif

                        <span class="badge badge-{{ $book->status === 'active' ? 'success' : 'secondary' }} ml-2">
                            {{ ucfirst($book->status) }}
                        </span>
                    </p>

                    <hr>

                    <h5>Description</h5>
                    <p>
                        {{ $book->description ?: 'No description provided.' }}
                    </p>

                    <hr>

                    <div class="d-flex">
                        <a href="{{ route('seller.books.edit', $book) }}" class="btn btn-primary mr-2">
                            Edit Book
                        </a>
                        <a href="{{ route('seller.books.index') }}" class="btn btn-secondary">
                            Back to list
                        </a>
                    </div>

                    <p class="text-muted mt-3 mb-0">
                        This is a preview of how customers will see your book details.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
