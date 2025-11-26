@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' | Bookly')
@section('page_title', 'Order #' . $order->id)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}">My Orders</a></li>
    <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Items</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Quantity</th>
                                <th>Unit Price (RM)</th>
                                <th>Total (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        @if ($item->book)
                                            <a href="{{ route('shop.books.show', $item->book) }}">
                                                {{ $item->book->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">[Deleted book]</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Order Total</th>
                                <th>{{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Meta --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Info</h3>
                </div>
                <div class="card-body">
                    <p><strong>Order ID:</strong> {{ $order->id }}</p>
                    <p><strong>Placed At:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    <p>
                        <strong>Status:</strong>
                        <span class="badge badge-secondary">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    {{-- Add shipping address, notes etc. later if you want --}}
                    <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">
                        Back to Orders
                    </a>

                    <a href="{{ route('customer.checkout.start', $order) }}"
                        class="btn btn-success {{ $order->status === 'paid' || $order->status === 'cancelled' ? 'd-none' : '' }}">
                        Make Payment
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
