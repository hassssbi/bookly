@extends('layouts.app')

@section('title', 'Categories | Bookly Admin')
@section('page_title', 'Categories Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Search + Filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <label for="q" class="mr-2">Search</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Name or slug">
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="sort" class="mr-2">Sort by</label>
                    <select name="sort" id="sort" class="form-control">
                        <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="id" {{ request('sort') === 'id' ? 'selected' : '' }}>ID</option>
                        <option value="created_at"{{ request('sort') === 'created_at' ? 'selected' : '' }}>Created At
                        </option>
                    </select>
                </div>

                <div class="form-group mr-2 mb-2">
                    <select name="direction" class="form-control">
                        <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending
                        </option>
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2 mr-2">
                    <i class="fas fa-search"></i> Apply
                </button>

                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary mb-2 mr-2">
                    Reset
                </a>

                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-2 ml-auto">
                    <i class="fas fa-plus"></i> New Category
                </a>
            </form>


        </div>
    </div>

    {{-- <div class="mb-3">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Category
        </a>
    </div> --}}

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Categories</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        {{-- Sortable column headers --}}
                        <th>
                            <a
                                href="{{ route('admin.categories.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('direction') === 'asc' && request('sort') === 'id' ? 'desc' : 'asc'])) }}">
                                ID
                                @if (request('sort') === 'id')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.categories.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('direction') === 'asc' && request('sort') === 'name' ? 'desc' : 'asc'])) }}">
                                Name
                                @if (request('sort', 'name') === 'name')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>
                            <a
                                href="{{ route('admin.categories.index', array_merge(request()->all(), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' && request('sort') === 'created_at' ? 'desc' : 'asc'])) }}">
                                Created At
                                @if (request('sort') === 'created_at')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ Str::limit($category->description, 50) }}</td>
                            <td>{{ $category->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" data-toggle="modal"
                                    data-target="#deleteCategoryModal{{ $category->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                {{-- Delete confirmation modal --}}
                                <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Category</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete category
                                                    <strong>{{ $category->name }}</strong>?
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
                            <td colspan="6" class="text-center">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            {{ $categories->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
