<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $items = Wishlist::with('book.category')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.wishlist.index', compact('items'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::where('id', $data['book_id'])
            ->where('status', 'active')
            ->firstOrFail();

        Wishlist::firstOrCreate([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        return back()->with('status', 'The book has been added to your wishlist.');
    }

    public function destroy(Request $request, Wishlist $wishlist)
    {
        $user = $request->user();

        if ($wishlist->user_id !== $user->id) {
            abort(403);
        }

        $wishlist->delete();

        return back()->with('status', 'Removed from wishlist.');
    }
}
