@extends('layouts.app')

@section('title', 'Show User | Bookly Admin')
@section('page_title', 'User Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="#" method="POST">

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" readonly class="form-control-plaintext"
                        value="{{ old('name', $user->name) }}">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" readonly class="form-control-plaintext"
                        value="{{ old('email', $user->email) }}">
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <input type="text" name="role" readonly class="form-control-plaintext"
                        value="{{ old('role', ucfirst($user->role)) }}">
                </div>

                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
