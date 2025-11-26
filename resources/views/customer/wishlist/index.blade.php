@extends('layouts.app')

@section('title', 'My Wishlist | Bookly')
@section('page_title', 'My Wishlist')

@section('breadcrumbs')
    <li class="breadcrumb-item active">My Wishlist</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Saved Books</h3>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($items as $item)
                    @php $book = $item->book; @endphp
                    @if (!$book)
                        @continue
                    @endif
                    <div class="col-sm-6 col-md-4 col-lg-3 d-flex">
                        <div class="card mb-3 flex-fill shadow">
                            @if ($book->cover_path)
                                <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}"
                                    class="card-img-top rounded-top" sizes="max-height: 150px">
                            @else
                                <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                    <span class="text-muted">No image</span>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">



                                {{-- <div class="text-center mb-2">

                                </div> --}}

                                <h5 class="card-title">
                                    <a href="{{ route('shop.books.show', $book) }}">
                                        {{ $book->title }}
                                    </a>
                                </h5>

                                <p class="text-muted mb-1">
                                    {{ $book->category->name ?? 'Uncategorized' }}
                                </p>
                                <p class="font-weight-bold mb-2">
                                    RM {{ number_format($book->price, 2) }}
                                </p>

                                <div class="mt-auto">
                                    <a href="{{ route('shop.books.show', $book) }}"
                                        class="btn btn-sm btn-primary btn-block mb-2">
                                        View
                                    </a>

                                    <form action="{{ route('customer.wishlist.destroy', $item) }}" method="POST"
                                        onsubmit="return confirm('Remove from wishlist?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-block">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted mb-0">You have no items in your wishlist.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
