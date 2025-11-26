@extends('layouts.app')

@section('title', 'My Orders | Bookly')
@section('page_title', 'My Orders')

@section('breadcrumbs')
    <li class="breadcrumb-item active">My Orders</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Order History</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Placed At</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Total (RM)</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->items->count() }}</td>
                            <td>{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-3">
                                You have no orders yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
