<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Step 1: from book page -> store book + qty in session
    public function start(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $book = Book::where('id', $data['book_id'])
            ->where('status', 'active')
            ->firstOrFail();

        if ($book->stock < $data['quantity']) {
            return back()->withErrors([
                'book' => 'Not enough stock available.',
            ])->withInput();
        }

        // Optional: prevent buying own book if user is seller too
        if ($book->seller_id === $user->id) {
            return back()->withErrors([
                'book' => 'You cannot order your own book.',
            ]);
        }

        session([
            'checkout' => [
                'book_id' => $book->id,
                'quantity' => $data['quantity'],
            ],
        ]);

        return redirect()->route('customer.checkout.show');
    }

    // Step 2: show checkout summary + choose payment method
    public function show(Request $request)
    {
        $checkout = session('checkout');

        if (! $checkout) {
            return redirect()->route('shop.index')
                ->with('status', 'Your checkout session has expired. Please try again.');
        }

        $book = Book::where('id', $checkout['book_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $qty = $checkout['quantity'];
        $total = $book->price * $qty;

        $paymentMethods = [
            'fpx' => 'FPX Online Banking',
            'card' => 'Credit / Debit Card',
            'ewallet' => 'E-Wallet',
        ];

        return view('customer.checkout.show', compact('book', 'qty', 'total', 'paymentMethods'));
    }

    // Step 3: confirm -> create order (pending_payment) and redirect to mock gateway
    public function confirm(Request $request)
    {
        $checkout = session('checkout');

        if (! $checkout) {
            return redirect()->route('shop.index')
                ->with('status', 'Your checkout session has expired. Please try again.');
        }

        $data = $request->validate([
            'payment_method' => 'required|in:fpx,card,ewallet',
        ]);

        $user = $request->user();

        $book = Book::where('id', $checkout['book_id'])
            ->where('status', 'active')
            ->lockForUpdate()
            ->firstOrFail();

        $qty = $checkout['quantity'];

        if ($book->stock < $qty) {
            return redirect()->route('shop.books.show', $book)
                ->withErrors(['book' => 'Not enough stock available.']);
        }

        $order = DB::transaction(function () use ($user, $book, $qty, $data) {
            $unitPrice = $book->price;
            $total = $unitPrice * $qty;

            $order = new Order;
            $order->buyer_id = $user->id;
            $order->status = 'pending';
            $order->total_amount = $total;
            $order->payment_method = $data['payment_method'];
            $order->save();

            $item = new OrderItem;
            $item->order_id = $order->id;
            $item->book_id = $book->id;
            $item->quantity = $qty;
            $item->unit_price = $unitPrice;
            $item->subtotal = $total;
            $item->save();

            // Reserve stock immediately (simple)
            $book->decrement('stock', $qty);

            return $order;
        });

        // Clear checkout
        $request->session()->forget('checkout');

        return redirect()->route('customer.payment.show', $order);
    }
}
