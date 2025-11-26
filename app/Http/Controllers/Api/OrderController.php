<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // CUSTOMER: list own orders
    public function index(Request $request)
    {
        return $request->user()
            ->orders()
            ->with(['items.book'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    // CUSTOMER: create order
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($user, $data) {
            $total = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                /** @var Book $book */
                $book = Book::lockForUpdate()->find($item['book_id']);
                $qty = $item['quantity'];

                if ($book->status !== 'active') {
                    abort(422, 'Book "'.$book->title.'" is not available.');
                }

                if ($book->stock < $qty) {
                    abort(422, 'Not enough stock for book: '.$book->title);
                }

                $subtotal = $book->price * $qty;
                $total += $subtotal;

                $orderItems[] = [
                    'book_id' => $book->id,
                    'quantity' => $qty,
                    'unit_price' => $book->price,
                    'subtotal' => $subtotal,
                ];

                // reduce stock
                $book->decrement('stock', $qty);
            }

            $order = Order::create([
                'buyer_id' => $user->id,
                'total_amount' => $total,
                'status' => 'paid', // or 'pending' if you want payment later
                'payment_method' => $data['payment_method'] ?? null,
            ]);

            foreach ($orderItems as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            return response()->json($order->load('items.book'), 201);
        });
    }

    // CUSTOMER: view single order
    public function show(Request $request, Order $order)
    {
        if ($order->buyer_id !== $request->user()->id) {
            return response()->json(['message' => 'You can only view your own orders'], 403);
        }

        return $order->load('items.book');
    }
}
