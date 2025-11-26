@extends('layouts.app')

@section('title', 'Users | Bookly Admin')
@section('page_title', 'Users Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    {{-- Search + Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <label for="q" class="mr-2">Search</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Name or email">
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="role" class="mr-2">Role</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">All</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="seller" {{ request('role') === 'seller' ? 'selected' : '' }}>Seller</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="sort" class="mr-2">Sort by</label>
                    <select name="sort" id="sort" class="form-control">
                        <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>
                            Registered At</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="role" {{ request('sort') === 'role' ? 'selected' : '' }}>Role</option>
                        <option value="id" {{ request('sort') === 'id' ? 'selected' : '' }}>ID</option>
                    </select>
                </div>

                <div class="form-group mr-2 mb-2">
                    <select name="direction" class="form-control">
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Descending
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2 mr-2">
                    <i class="fas fa-search"></i> Apply
                </button>

                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mb-2">
                    Reset
                </a>

                <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-2 ml-auto">
                    <i class="fas fa-plus"></i> New User
                </a>
            </form>
        </div>
    </div>

    <div class="mb-3">

    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Users</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>
                            <a
                                href="{{ route('admin.users.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('direction') === 'asc' && request('sort') === 'id' ? 'desc' : 'asc'])) }}">
                                ID
                                @if (request('sort') === 'id')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.users.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('direction') === 'asc' && request('sort') === 'name' ? 'desc' : 'asc'])) }}">
                                Name
                                @if (request('sort', 'name') === 'name')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.users.index', array_merge(request()->all(), ['sort' => 'email', 'direction' => request('direction') === 'asc' && request('sort') === 'email' ? 'desc' : 'asc'])) }}">
                                Email
                                @if (request('sort', 'email') === 'email')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.users.index', array_merge(request()->all(), ['sort' => 'role', 'direction' => request('direction') === 'asc' && request('sort') === 'role' ? 'desc' : 'asc'])) }}">
                                Role
                                @if (request('sort', 'role') === 'role')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.users.index', array_merge(request()->all(), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' && request('sort') === 'created_at' ? 'desc' : 'asc'])) }}">
                                Registered At
                                @if (request('sort') === 'created_at')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-secondary">{{ $user->role }}</span>
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-pen"></i>
                                </a>

                                @if (auth()->user()->id != $user->id)
                                    {{-- <form action="{{ route('admin.users.updateRole', $user) }}" method="POST"
                                        class="d-inline-block mb-1">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="form-control form-control-sm d-inline-block"
                                            style="width: 110px;">
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin
                                            </option>
                                            <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Seller
                                            </option>
                                            <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>
                                                Customer
                                            </option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            Save
                                        </button>
                                    </form> --}}
                                    <button class="btn btn-sm btn-danger" data-toggle="modal"
                                        data-target="#deleteUserModal{{ $user->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                                {{-- Quick role change (optional) --}}

                                {{-- Delete --}}


                                <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete User</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete user
                                                    <strong>{{ $user->name }}</strong> ({{ $user->email }})?
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
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
