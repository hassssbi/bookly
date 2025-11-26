<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // CUSTOMER: list wishlist items
    public function index(Request $request)
    {
        return Wishlist::with('book')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // CUSTOMER: add to wishlist
    public function store(Request $request)
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'book_id' => $data['book_id'],
        ]);

        return response()->json($wishlist->load('book'), 201);
    }

    // CUSTOMER: remove from wishlist
    public function destroy(Request $request, Wishlist $wishlist)
    {
        if ($wishlist->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You can only remove your own wishlist items'], 403);
        }

        $wishlist->delete();

        return response()->json(null, 204);
    }
}
