<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // List customer's orders
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::where('buyer_id', $user->id)
            ->with('items.book')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    // View a single order
    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->buyer_id !== $user->id) {
            abort(403);
        }

        $order->load('items.book');

        return view('customer.orders.show', compact('order'));
    }

    // Create an order: book_id + quantity -> one order, one item
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ]);

        /** @var Book $book */
        $book = Book::where('id', $data['book_id'])
            ->where('status', 'active')
            ->firstOrFail();

        // Prevent seller buying their own book (optional)
        if ($book->seller_id === $user->id) {
            return back()->withErrors([
                'book' => 'You cannot order your own book.',
            ]);
        }

        if ($book->stock < $data['quantity']) {
            return back()->withErrors([
                'book' => 'Not enough stock available.',
            ])->withInput();
        }

        // Wrap in transaction
        $order = DB::transaction(function () use ($user, $book, $data) {
            $qty = $data['quantity'];
            $unitPrice = $book->price;
            $total = $unitPrice * $qty;

            $order = new Order;
            $order->buyer_id = $user->id;
            $order->status = 'pending'; // or 'new'
            $order->total_amount = $total;
            // optional: shipping_address, notes, etc.
            $order->save();

            $item = new OrderItem;
            $item->order_id = $order->id;
            $item->book_id = $book->id;
            $item->quantity = $qty;
            $item->unit_price = $unitPrice;
            $item->subtotal = $total;
            $item->save();

            // Reduce stock
            $book->decrement('stock', $qty);

            return $order;
        });

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('status', 'Your order has been placed successfully.');
    }
}
