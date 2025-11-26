{{-- resources/views/shop/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Shop | Bookly')
@section('page_title', 'Shop')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Shop</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @php
        $wishlistBookIds = $wishlistBookIds ?? [];
    @endphp
    {{-- Filters + search --}}
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('shop.index') }}" method="GET" class="row">
                <div class="col-md-4 mb-2">
                    <label class="mb-1">Search</label>
                    <input type="text" name="q" class="form-control" placeholder="Search by title..."
                        value="{{ request('q') }}">
                </div>

                <div class="col-md-3 mb-2">
                    <label class="mb-1">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label class="mb-1">Min Price</label>
                    <input type="number" step="0.01" name="min_price" class="form-control"
                        value="{{ request('min_price') }}">
                </div>

                <div class="col-md-2 mb-2">
                    <label class="mb-1">Max Price</label>
                    <input type="number" step="0.01" name="max_price" class="form-control"
                        value="{{ request('max_price') }}">
                </div>

                <div class="col-md-1 d-flex align-items-end mb-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="mt-2">
                <a href="{{ route('shop.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset
                </a>
            </div>
        </div>
    </div>

    {{-- Books grid --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Books</h3>
            <div class="card-tools">
                @if ($books->total() > 0)
                    <span class="mr-2 text-muted" style="font-size: 0.9rem;">
                        Showing {{ $books->firstItem() }}–{{ $books->lastItem() }}
                        of {{ $books->total() }} books
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if ($books->count() === 0)
                <p class="text-muted mb-0">No books found. Try adjusting your filters.</p>
            @else
                <div class="row">
                    @foreach ($books as $book)
                        <div class="col-sm-6 col-md-4 col-lg-3 d-flex">
                            <div class="card mb-4 flex-fill">
                                @if ($book->cover_path)
                                    <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}"
                                        class="card-img-top rounded-top" sizes="max-height: 150px">
                                @else
                                    <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                        <span class="text-muted">No image</span>
                                    </div>
                                @endif

                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title mb-1" title="{{ $book->title }}">
                                        <a href="{{ route('shop.books.show', $book) }}">
                                            {{ \Illuminate\Support\Str::limit($book->title, 40) }}
                                        </a>
                                    </h6>

                                    <p class="mb-1 text-muted" style="font-size: 0.85rem;">
                                        {{ $book->category->name ?? 'Uncategorized' }}
                                    </p>

                                    <p class="mb-1 font-weight-bold">
                                        RM {{ number_format($book->price, 2) }}
                                    </p>

                                    <p class="mb-2" style="font-size: 0.8rem;">
                                        @if ($book->stock > 0)
                                            <span class="badge badge-success">In stock</span>
                                        @else
                                            <span class="badge badge-danger">Out of stock</span>
                                        @endif
                                    </p>

                                    <div class="mt-auto">
                                        <a href="{{ route('shop.books.show', $book) }}"
                                            class="btn btn-sm btn-primary btn-block mb-1">
                                            View Details
                                        </a>
                                        @auth
                                            @if (auth()->user()->role === 'customer')
                                                @php
                                                    $inWishlist = in_array($book->id, $wishlistBookIds, true);
                                                @endphp

                                                @if ($inWishlist)
                                                    {{-- Already in wishlist: show disabled button --}}
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-block"
                                                        disabled>
                                                        <i class="far fa-heart"></i> In Wishlist
                                                    </button>
                                                @else
                                                    {{-- Not in wishlist yet: show active form --}}
                                                    <form action="{{ route('customer.wishlist.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-secondary btn-block">
                                                            <i class="far fa-heart"></i> Add to Wishlist
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endauth

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


            @endif
        </div>
        <div class="card-footer">
            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $books->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
