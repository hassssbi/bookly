@extends('layouts.app')

@section('title', 'My Books | Bookly')
@section('page_title', 'My Books')

@section('breadcrumbs')
    <li class="breadcrumb-item active">My Books</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('seller.books.index') }}" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <label for="q" class="mr-2">Search</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Title">
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="category_id" class="mr-2">Category</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">All</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="status" class="mr-2">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2 mr-2">
                    <i class="fas fa-search"></i> Filter
                </button>

                <a href="{{ route('seller.books.index') }}" class="btn btn-secondary mb-2">
                    Reset
                </a>

                <a href="{{ route('seller.books.create') }}" class="btn btn-primary mb-2 ml-auto">
                    <i class="fas fa-plus"></i> Add New Book
                </a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">My Books</h3>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Cover</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>
                                @if ($book->cover_path)
                                    <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}"
                                        class="img-fluid" style="max-height: 60px;">
                                @else
                                    <div class="bg-light text-muted d-flex align-items-center justify-content-center"
                                        style="height:60px;width:45px;">
                                        <span class="small">No image</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('seller.books.show', $book) }}">
                                    {{ $book->title }}
                                </a><br>
                                <small class="text-muted">{{ $book->slug }}</small>
                            </td>
                            <td>{{ $book->category->name ?? '-' }}</td>
                            <td>RM {{ number_format($book->price, 2) }}</td>
                            <td>{{ $book->stock }}</td>
                            <td>
                                <span class="badge badge-{{ $book->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('seller.books.show', $book) }}" class="btn btn-sm btn-primary mb-1">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('seller.books.edit', $book) }}" class="btn btn-sm btn-info mb-1">
                                    <i class="fas fa-pen"></i>
                                </a>

                                {{-- <form action="{{ route('seller.books.destroy', $book) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this book?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form> --}}

                                <button class="btn btn-sm btn-danger mb-1" data-toggle="modal"
                                    data-target="#deleteBookModal{{ $book->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <div class="modal fade" id="deleteBookModal{{ $book->id }}" tabindex="-1">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('seller.books.destroy', $book) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Book</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete book
                                                    <strong>{{ $book->title }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-3">No books yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $books->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
