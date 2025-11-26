@extends('layouts.app')

@section('title', 'Edit Book | Bookly')
@section('page_title', 'Edit Book')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('seller.books.index') }}">My Books</a></li>
    <li class="breadcrumb-item active">{{ $book->title }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('seller.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}"
                        class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Category</label>
                        <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Price (RM)</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $book->price) }}"
                            class="form-control @error('price') is-invalid @enderror" required>
                        @error('price')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', $book->stock) }}"
                            class="form-control @error('stock') is-invalid @enderror" required>
                        @error('stock')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $book->status) === 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="inactive" {{ old('status', $book->status) === 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Description (optional)</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $book->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Current Cover</label><br>
                    @if ($book->cover_path)
                        <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}"
                            style="max-height: 150px;">
                    @else
                        <span class="text-muted">No cover uploaded.</span>
                    @endif
                </div>

                <div class="form-group">
                    <label>Change Cover (optional)</label>
                    <input type="file" name="cover" class="form-control-file @error('cover') is-invalid @enderror"
                        accept="image/*">
                    @error('cover')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Uploading a new image will replace the old one.</small>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('seller.books.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
