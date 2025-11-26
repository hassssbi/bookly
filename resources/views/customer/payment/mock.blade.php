@extends('layouts.app')

@section('title', 'Payment for Order #' . $order->id)
@section('page_title', 'Mock Payment Gateway')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}">My Orders</a></li>
    <li class="breadcrumb-item active">Payment for Order #{{ $order->id }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Order Summary</h3>
                </div>
                <div class="card-body">
                    <p><strong>Order ID:</strong> {{ $order->id }}</p>
                    <p><strong>Amount:</strong> RM {{ number_format($order->total_amount, 2) }}</p>
                    <p><strong>Payment Method:</strong>
                        @php
                            $labels = [
                                'fpx' => 'FPX Online Banking',
                                'card' => 'Credit / Debit Card',
                                'ewallet' => 'E-Wallet',
                            ];
                        @endphp
                        {{ $labels[$order->payment_method] ?? $order->payment_method }}
                    </p>

                    <hr>

                    <h5>Items</h5>
                    <ul class="list-unstyled mb-0">
                        @foreach ($order->items as $item)
                            <li>
                                {{ $item->book->title ?? '[Deleted book]' }}
                                &times; {{ $item->quantity }}
                                (RM {{ number_format($item->subtotal, 2) }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mock Payment</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        This is a mock payment screen. Click "Pay Now" to simulate a successful payment,
                        or "Cancel Payment" to cancel.
                    </p>

                    <form action="{{ route('customer.payment.complete', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block">
                            Pay Now
                        </button>
                    </form>

                    <form action="{{ route('customer.payment.cancel', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block">
                            Cancel Payment
                        </button>
                    </form>

                    <a href="{{ route('customer.orders.index') }}" class="btn btn-link btn-block mt-2">
                        Back to My Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
