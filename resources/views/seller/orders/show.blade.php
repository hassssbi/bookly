@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' (Seller View)')
@section('page_title', 'Order #' . $order->id . ' (Seller View)')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('seller.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="row">
        {{-- Items for this seller --}}
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Items Sold by You</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Qty</th>
                                <th>Unit Price (RM)</th>
                                <th>Total (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
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
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total for your items</th>
                                <th>{{ number_format($totalForSeller, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Order meta info --}}
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Order Info</h3>
                </div>
                <div class="card-body">
                    <p><strong>Order ID:</strong> {{ $order->id }}</p>
                    <p><strong>Placed At:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    <p>
                        <strong>Status:</strong>
                        <span class="badge badge-secondary">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </p>
                    <p><strong>Customer:</strong> {{ $order->buyer->name ?? 'Unknown' }}</p>
                    <p><strong>Total Order Amount:</strong> RM {{ number_format($order->total_amount, 2) }}</p>
                    <p><strong>Your Portion:</strong> RM {{ number_format($totalForSeller, 2) }}</p>

                    {{-- Add shipping address / notes later if you have columns --}}

                    <a href="{{ route('seller.orders.index') }}" class="btn btn-secondary">
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
