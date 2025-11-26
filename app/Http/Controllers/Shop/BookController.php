<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Shop home: list books with filters
    public function index(Request $request)
    {
        $query = Book::with('category', 'seller')
            ->where('status', 'active')
            ->where('stock', '>', 0);

        if ($search = $request->input('q')) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        $books = $query
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        // ✅ collect wishlist book IDs for the logged-in customer
        $wishlistBookIds = [];
        if (auth()->check() && auth()->user()->role === 'customer') {
            $wishlistBookIds = Wishlist::where('user_id', auth()->id())
                ->pluck('book_id')
                ->toArray();
        }

        return view('shop.index', compact('books', 'categories', 'wishlistBookIds'));
    }

    // Single book hero page
    /* public function show(Book $book)
    {
        // Only show if active
        if ($book->status !== 'active') {
            abort(404);
        }

        $book->load('category', 'seller');

        // Simple "related" books from same category
        $related = Book::where('category_id', $book->category_id)
            ->where('status', 'active')
            ->where('id', '!=', $book->id)
            ->limit(4)
            ->get();

        return view('shop.books.show', compact('book', 'related'));
    } */

    public function show(Book $book)
    {
        // Only show if active
        if ($book->status !== 'active') {
            abort(404);
        }

        $book->load(['category', 'seller']);

        $inWishlist = false;

        if (auth()->check() && auth()->user()->role === 'customer') {
            $inWishlist = Wishlist::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->exists();
        }

        // Simple "related" books from same category
        $related = Book::where('category_id', $book->category_id)
            ->where('status', 'active')
            ->where('id', '!=', $book->id)
            ->limit(4)
            ->get();

        return view('shop.books.show', compact('book', 'inWishlist', 'related'));
    }
}
