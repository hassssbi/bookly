<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerProfile;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }

        if ($user->role === 'seller') {
            return $this->sellerDashboard($user);
        }

        // Customers: go to shop, not a dashboard
        return redirect()->route('shop.index');
        // If you want a customer dashboard later:
        // return $this->customerDashboard($user);
    }

    protected function adminDashboard()
    {
        // High-level counts
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalSellers = User::where('role', 'seller')->count();
        $pendingSellers = SellerProfile::where('status', 'pending')->count();

        $totalBooks = Book::count();
        $activeBooks = Book::where('status', 'active')->count();
        $outOfStockBooks = Book::where('stock', '<=', 0)->count();

        $totalOrders = Order::count();
        $paidOrders = Order::where('status', 'paid')->count();
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');

        // Orders last 7 days (for a mini chart)
        $fromDate = Carbon::now()->subDays(6)->startOfDay();
        $dailyOrders = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as revenue')
        )
            ->where('created_at', '>=', $fromDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Top 5 categories by revenue
        $topCategories = Category::select('categories.id', 'categories.name',
            DB::raw('SUM(order_items.subtotal) as revenue')
        )
            ->join('books', 'books.category_id', '=', 'categories.id')
            ->join('order_items', 'order_items.book_id', '=', 'books.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'paid')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Top 5 sellers by revenue
        $topSellers = User::select('users.id', 'users.name',
            DB::raw('SUM(order_items.subtotal) as revenue')
        )
            ->join('books', 'books.seller_id', '=', 'users.id')
            ->join('order_items', 'order_items.book_id', '=', 'books.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('users.role', 'seller')
            ->where('orders.status', 'paid')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalUsers',
            'totalCustomers',
            'totalSellers',
            'pendingSellers',
            'totalBooks',
            'activeBooks',
            'outOfStockBooks',
            'totalOrders',
            'paidOrders',
            'totalRevenue',
            'dailyOrders',
            'topCategories',
            'topSellers'
        ));
    }

    protected function sellerDashboard(User $seller)
    {
        // Total books owned by this seller
        $totalBooks = Book::where('seller_id', $seller->id)->count();
        $activeBooks = Book::where('seller_id', $seller->id)
            ->where('status', 'active')
            ->count();

        // Orders that include this seller's books
        $sellerItemsQuery = OrderItem::whereHas('book', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })
            ->whereHas('order', function ($q) {
                $q->where('status', 'paid');
            });

        $totalUnitsSold = (clone $sellerItemsQuery)->sum('quantity');
        $totalRevenue = (clone $sellerItemsQuery)->sum('subtotal');

        // Last 7 days revenue
        $fromDate = Carbon::now()->subDays(6)->startOfDay();
        $dailyRevenue = OrderItem::select(
            DB::raw('DATE(orders.created_at) as date'),
            DB::raw('SUM(order_items.subtotal) as revenue')
        )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('books', 'books.id', '=', 'order_items.book_id')
            ->where('books.seller_id', $seller->id)
            ->where('orders.status', 'paid')
            ->where('orders.created_at', '>=', $fromDate)
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->orderBy('date')
            ->get();

        // Top 5 books by revenue for this seller
        $topBooks = Book::select('books.id', 'books.title',
            DB::raw('SUM(order_items.subtotal) as revenue'),
            DB::raw('SUM(order_items.quantity) as units')
        )
            ->join('order_items', 'order_items.book_id', '=', 'books.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('books.seller_id', $seller->id)
            ->where('orders.status', 'paid')
            ->groupBy('books.id', 'books.title')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Recent orders involving this seller
        $recentOrderItems = OrderItem::with(['order.buyer', 'book'])
            ->whereHas('book', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.seller', compact(
            'totalBooks',
            'activeBooks',
            'totalUnitsSold',
            'totalRevenue',
            'dailyRevenue',
            'topBooks',
            'recentOrderItems'
        ));
    }

    // Optional if you want a customer dashboard one day

    /* protected function customerDashboard(User $customer)
    {
        $totalOrders = Order::where('buyer_id', $customer->id)->count();
        $paidOrders = Order::where('buyer_id', $customer->id)->where('status', 'paid')->count();
        $totalSpent = Order::where('buyer_id', $customer->id)->where('status', 'paid')->sum('total_amount');
        $wishlistCount = Wishlist::where('user_id', $customer->id)->count();

        $recentOrders = Order::where('buyer_id', $customer->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.customer', compact(
            'totalOrders', 'paidOrders', 'totalSpent', 'wishlistCount', 'recentOrders'
        ));
    } */
}
