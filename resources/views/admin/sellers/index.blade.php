@extends('layouts.app')

@section('title', 'Sellers | Bookly Admin')
@section('page_title', 'Seller Approvals')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Sellers</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Search + Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sellers.index') }}" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <label for="q" class="mr-2">Search</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Name, email, store name">
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="status" class="mr-2">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="sort" class="mr-2">Sort by</label>
                    <select name="sort" id="sort" class="form-control">
                        <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>
                            Applied At</option>
                        <option value="store_name" {{ request('sort') === 'store_name' ? 'selected' : '' }}>Store Name
                        </option>
                        <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status</option>
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

                <a href="{{ route('admin.sellers.index') }}" class="btn btn-secondary mb-2">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Seller Profiles</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>
                            <a
                                href="{{ route('admin.sellers.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('direction') === 'asc' && request('sort') === 'id' ? 'desc' : 'asc'])) }}">
                                ID
                                @if (request('sort') === 'id')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>User</th>
                        <th>
                            <a
                                href="{{ route('admin.sellers.index', array_merge(request()->all(), ['sort' => 'store_name', 'direction' => request('direction') === 'asc' && request('sort') === 'store_name' ? 'desc' : 'asc'])) }}">
                                Store Name
                                @if (request('sort', 'store_name') === 'store_name')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.sellers.index', array_merge(request()->all(), ['sort' => 'status', 'direction' => request('direction') === 'asc' && request('sort') === 'status' ? 'desc' : 'asc'])) }}">
                                Status
                                @if (request('sort', 'status') === 'status')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ route('admin.sellers.index', array_merge(request()->all(), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' && request('sort') === 'created_at' ? 'desc' : 'asc'])) }}">
                                Applied At
                                @if (request('sort') === 'created_at')
                                    <i class="fas fa-sort-{{ request('direction', 'asc') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th style="width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sellers as $seller)
                        <tr>
                            <td>{{ $seller->id }}</td>
                            <td>
                                {{ $seller->user->name }}<br>
                                <small>{{ $seller->user->email }}</small>
                            </td>
                            <td>{{ $seller->store_name }}</td>
                            <td>
                                @php
                                    $badge = match ($seller->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge badge-{{ $badge }}">{{ ucfirst($seller->status) }}</span>
                            </td>
                            <td>{{ $seller->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.sellers.show', $seller) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Quick Approve --}}
                                <form action="{{ route('admin.sellers.updateStatus', $seller) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success"
                                        {{ $seller->status === 'approved' ? 'disabled' : '' }}>
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>

                                {{-- Reject with reason (modal like before) --}}
                                <button class="btn btn-sm btn-danger" data-toggle="modal"
                                    data-target="#rejectModal{{ $seller->id }}"
                                    {{ $seller->status === 'rejected' ? 'disabled' : '' }}>
                                    <i class="fas fa-ban"></i> Reject
                                </button>

                                <div class="modal fade" id="rejectModal{{ $seller->id }}" tabindex="-1">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('admin.sellers.updateStatus', $seller) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Seller</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Rejection reason</label>
                                                        <textarea name="rejection_reason" rows="3" class="form-control" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Reject</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No seller profiles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            {{ $sellers->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
