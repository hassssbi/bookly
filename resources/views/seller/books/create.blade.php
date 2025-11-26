@extends('layouts.app')

@section('title', 'Add New Book | Bookly')
@section('page_title', 'Add New Book')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('seller.books.index') }}">My Books</a></li>
    <li class="breadcrumb-item active">Add New Book</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('seller.books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Category</label>
                        <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                            class="form-control @error('price') is-invalid @enderror" required>
                        @error('price')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Stock</label>
                        <input type="number" name="stock" value="{{ old('stock') }}"
                            class="form-control @error('stock') is-invalid @enderror" required>
                        @error('stock')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Description (optional)</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Cover Image (optional)</label>
                    <input type="file" name="cover" class="form-control-file @error('cover') is-invalid @enderror"
                        accept="image/*">
                    @error('cover')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Max 2MB. JPG/PNG.</small>
                </div>

                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('seller.books.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
