<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // List all order items related to this seller's books
    public function index(Request $request)
    {
        $seller = $request->user();

        $query = OrderItem::with(['order.buyer', 'book'])
            ->whereHas('book', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            });

        // Optional small filters
        if ($status = $request->input('status')) {
            $query->whereHas('order', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        if ($from = $request->input('from')) {
            $query->whereHas('order', function ($q) use ($from) {
                $q->whereDate('created_at', '>=', $from);
            });
        }

        if ($to = $request->input('to')) {
            $query->whereHas('order', function ($q) use ($to) {
                $q->whereDate('created_at', '<=', $to);
            });
        }

        $items = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // For status filter dropdown
        $statuses = ['pending', 'paid', 'cancelled'];

        return view('seller.orders.index', compact('items', 'statuses'));
    }

    // Show one order but only the items that belong to this seller
    public function show(Request $request, Order $order)
    {
        $seller = $request->user();

        // load items + books + buyer
        $order->load(['items.book', 'buyer']);

        // Filter items to only seller's books
        $sellerItems = $order->items->filter(function ($item) use ($seller) {
            return $item->book && $item->book->seller_id === $seller->id;
        });

        if ($sellerItems->isEmpty()) {
            // This order has nothing to do with this seller
            abort(403);
        }

        $totalForSeller = $sellerItems->sum('subtotal');

        return view('seller.orders.show', [
            'order' => $order,
            'items' => $sellerItems,
            'totalForSeller' => $totalForSeller,
        ]);
    }
}
