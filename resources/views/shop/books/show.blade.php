@extends('layouts.app')

@section('title', $book->title . ' | Book Details')
@section('page_title', $book->title)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
    <li class="breadcrumb-item active">{{ $book->title }}</li>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <div class="row">
        {{-- Image --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-body text-center">
                    @if ($book->cover_path)
                        <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="img-fluid"
                            style="max-height: 350px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 350px;">
                            <span class="text-muted">No image</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Details + order form --}}
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="mb-2">{{ $book->title }}</h2>
                    <p class="text-muted mb-1">
                        Category: {{ $book->category->name ?? '-' }}
                    </p>
                    <p class="text-muted mb-1">
                        Seller: {{ $book->seller->name ?? 'Unknown' }}
                    </p>

                    <h3 class="text-success mb-3">
                        RM {{ number_format($book->price, 2) }}
                    </h3>

                    <p class="mb-3">
                        @if ($book->stock > 0)
                            <span class="badge badge-success">In stock ({{ $book->stock }})</span>
                        @else
                            <span class="badge badge-danger">Out of stock</span>
                        @endif
                    </p>

                    <hr>

                    <h5>Description</h5>
                    <p>{{ $book->description ?: 'No description provided.' }}</p>
                </div>
            </div>

            {{-- Buy now (customer only, with confirmation modal) --}}
            @auth
                @if (auth()->user()->role === 'customer' && $book->stock > 0)
                    <div class="card">
                        <div class="card-body">
                            <form id="buy-now-form" action="{{ route('customer.checkout.start') }}" method="POST">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <div class="form-inline">
                                    <div class="form-group mr-2 mb-2">
                                        <label for="quantity" class="mr-2">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" class="form-control" min="1"
                                            max="{{ $book->stock }}" value="1" required>
                                    </div>

                                    <button type="button" class="btn btn-primary mb-2" data-toggle="modal"
                                        data-target="#confirmBuyModal">
                                        Buy Now
                                    </button>

                                </div>
                            </form>

                            @auth
                                @if (auth()->user()->role === 'customer')
                                    <form action="{{ route('customer.wishlist.store') }}" method="POST" class="mb-3">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                                            <i class="far fa-heart"></i> Add to Wishlist
                                        </button>
                                    </form>
                                @endif
                            @endauth

                            <small class="text-muted">
                                You will be redirected to a mock payment page after confirming.
                            </small>
                        </div>
                    </div>

                    {{-- Confirmation modal --}}
                    <div class="modal fade" id="confirmBuyModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Purchase</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>
                                        You are about to buy <strong id="confirm-qty-text">1</strong> copy(ies) of
                                        <strong>{{ $book->title }}</strong>.
                                    </p>
                                    <p>
                                        Price per unit: <strong>RM {{ number_format($book->price, 2) }}</strong><br>
                                        Total: <strong id="confirm-total-text">RM {{ number_format($book->price, 2) }}</strong>
                                    </p>
                                    <p class="text-muted mb-0">
                                        After confirming, you will choose a payment method and complete the mock payment.
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.getElementById('buy-now-form').submit();">
                                        Confirm &amp; Continue
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @push('scripts')
                        <script>
                            (function() {
                                var qtyInput = document.getElementById('quantity');
                                var qtyText = document.getElementById('confirm-qty-text');
                                var totalText = document.getElementById('confirm-total-text');
                                var price = {{ $book->price }};

                                function updateModal() {
                                    var qty = parseInt(qtyInput.value || '1', 10);
                                    if (isNaN(qty) || qty < 1) qty = 1;
                                    qtyText.textContent = qty;
                                    totalText.textContent = 'RM ' + (qty * price).toFixed(2);
                                }

                                if (qtyInput) {
                                    qtyInput.addEventListener('input', updateModal);
                                }

                                // Update when modal is opened
                                $('#confirmBuyModal').on('show.bs.modal', updateModal);
                            })
                            ();
                        </script>
                    @endpush
                @elseif(auth()->user()->role !== 'customer')
                    <div class="alert alert-secondary">
                        You are logged in as <strong>{{ auth()->user()->role }}</strong>.
                        Only customers can place orders.
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    Please <a href="{{ route('login') }}">log in</a> as a customer to place an order.
                </div>
            @endauth

        </div>
    </div>

    {{-- Related books --}}
    @if ($related->count())
        <hr>
        <h4>Related books</h4>
        <div class="row">
            @foreach ($related as $r)
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            @if ($r->cover_path)
                                <img src="{{ asset('storage/' . $r->cover_path) }}" alt="{{ $r->title }}"
                                    class="img-fluid" style="max-height: 140px;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height:140px;">
                                    <span class="text-muted">No image</span>
                                </div>
                            @endif
                            <h6 class="mt-2">
                                <a href="{{ route('shop.books.show', $r) }}">
                                    {{ Str::limit($r->title, 40) }}
                                </a>
                            </h6>
                            <p class="mb-0">RM {{ number_format($r->price, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
