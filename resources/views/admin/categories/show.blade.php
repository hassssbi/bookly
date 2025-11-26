@extends('layouts.app')

@section('title', 'Show Category | Bookly Admin')
@section('page_title', 'Category Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">{{ $category->name }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="#" method="POST">

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" readonly class="form-control-plaintext"
                        value="{{ old('name', $category->name) }}">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea readonly style="resize: none" name="description" id="description" rows="3"
                        class="form-control-plaintext">{{ old('description', $category->description) }}</textarea>
                </div>

                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </form>
        </div>
    </div>
@endsection
