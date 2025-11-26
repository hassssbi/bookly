<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // Mock payment page
    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->buyer_id !== $user->id) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('customer.orders.show', $order)
                ->with('status', 'This order is not awaiting payment.');
        }

        $order->load('items.book');

        return view('customer.payment.mock', compact('order'));
    }

    // Pay now
    public function complete(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->buyer_id !== $user->id) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('customer.orders.show', $order)
                ->with('status', 'This order is not awaiting payment.');
        }

        $order->status = 'paid';
        $order->updated_at = now();
        $order->save();

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('status', 'Payment successful! Your order is confirmed.');
    }

    // Cancel payment -> cancel order + restore stock
    public function cancel(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->buyer_id !== $user->id) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('customer.orders.show', $order)
                ->with('status', 'This order is not awaiting payment.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->book) {
                    $item->book->increment('stock', $item->quantity);
                }
            }

            $order->status = 'cancelled';
            $order->save();
        });

        return redirect()
            ->route('customer.orders.index')
            ->with('status', 'Payment cancelled. Your order has been cancelled.');
    }
}
