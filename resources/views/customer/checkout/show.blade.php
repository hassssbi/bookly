@extends('layouts.app')

@section('title', 'Checkout')
@section('page_title', 'Checkout')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
    <li class="breadcrumb-item active">Checkout</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Order Summary</h3>
                </div>
                <div class="card-body">
                    <h5>{{ $book->title }}</h5>
                    <p class="text-muted mb-1">
                        Category: {{ $book->category->name ?? '-' }}
                    </p>
                    <p class="mb-1">
                        Quantity: <strong>{{ $qty }}</strong>
                    </p>
                    <p class="mb-1">
                        Unit price: <strong>RM {{ number_format($book->price, 2) }}</strong>
                    </p>
                    <h4 class="mt-3">
                        Total: <strong>RM {{ number_format($total, 2) }}</strong>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Method</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.checkout.confirm') }}" method="POST">
                        @csrf

                        @foreach ($paymentMethods as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                    id="pm_{{ $value }}" value="{{ $value }}"
                                    {{ old('payment_method', 'fpx') === $value ? 'checked' : '' }}>
                                <label class="form-check-label" for="pm_{{ $value }}">
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach

                        @error('payment_method')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-primary btn-block mt-3">
                            Proceed to Payment
                        </button>

                        <a href="{{ route('shop.books.show', $book) }}" class="btn btn-secondary btn-block mt-2">
                            Back to Book
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
