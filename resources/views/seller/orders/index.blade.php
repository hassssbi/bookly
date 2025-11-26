@extends('layouts.app')

@section('title', 'Seller | Orders')
@section('page_title', 'Orders from Customers')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Orders</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('seller.orders.index') }}" class="form-inline">
                <div class="form-group mr-2 mb-2">
                    <label for="status" class="mr-2">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="from" class="mr-2">From</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}" class="form-control">
                </div>

                <div class="form-group mr-2 mb-2">
                    <label for="to" class="mr-2">To</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary mb-2 mr-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('seller.orders.index') }}" class="btn btn-secondary mb-2">
                    Reset
                </a>
            </form>
        </div>
    </div>

    {{-- Table of order items (each row = one book sold) --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Orders containing your books</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Placed At</th>
                        <th>Customer</th>
                        <th>Book</th>
                        <th>Qty</th>
                        <th>Line Total (RM)</th>
                        <th>Order Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->order->id }}</td>
                            <td>{{ $item->order->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $item->order->buyer->name ?? 'Unknown' }}</td>
                            <td>
                                @if ($item->book)
                                    <a href="{{ route('shop.books.show', $item->book) }}" target="_blank">
                                        {{ $item->book->title }}
                                    </a>
                                @else
                                    <span class="text-muted">[Deleted book]</span>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->subtotal, 2) }}</td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $item->order->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('seller.orders.show', $item->order) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-3">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $items->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
