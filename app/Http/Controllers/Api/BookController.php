<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    // PUBLIC: list books with filters
    public function index(Request $request)
    {
        $query = Book::with(['category', 'seller'])
            ->where('status', 'active');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    // PUBLIC: single book
    public function show(Book $book)
    {
        // Only show active books publicly
        if ($book->status !== 'active') {
            return response()->json(['message' => 'Book not available'], 404);
        }

        return $book->load(['category', 'seller']);
    }

    // SELLER: create book (via /seller/books)
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Only sellers can create books'], 403);
        }

        // 🔴 NEW CHECK: seller must have an APPROVED seller profile
        $profile = $user->sellerProfile; // relationship from User model

        if (! $profile || $profile->status !== 'approved') {
            return response()->json([
                'message' => 'Your seller account is not approved yet. Please wait for admin approval before adding books.',
            ], 403);
        }
        // 🔴 END NEW CHECK

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'cover' => 'nullable|image|max:2048', // max 2MB
        ]);

        $slug = Str::slug($data['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Book::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter++;
        }

        $book = new Book;
        $book->seller_id = $user->id;
        $book->category_id = $data['category_id'];
        $book->title = $data['title'];
        $book->slug = $slug;
        $book->description = $data['description'] ?? null;
        $book->price = $data['price'];
        $book->stock = $data['stock'];
        $book->status = $data['status'] ?? 'active';

        // handle cover upload
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public'); // storage/app/public/covers/...
            $book->cover_path = $path;
        }

        $book->save();

        return response()->json($book->load(['category', 'seller']), 201);
    }

    // SELLER: update own book
    public function update(Request $request, Book $book)
    {
        $user = $request->user();
        if ($user->role !== 'seller' || $book->seller_id !== $user->id) {
            return response()->json(['message' => 'You can only update your own books'], 403);
        }

        $profile = $user->sellerProfile;
        if (! $profile || $profile->status !== 'approved') {
            return response()->json([
                'message' => 'Your seller account is not approved anymore. You cannot modify books.',
            ], 403);
        }

        $data = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'cover' => 'nullable|image|max:2048',
        ]);

        if (isset($data['title']) && $data['title'] !== $book->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Book::where('slug', $slug)->where('id', '!=', $book->id)->exists()) {
                $slug = $originalSlug.'-'.$counter++;
            }
            $book->slug = $slug;
        }

        if (isset($data['category_id'])) {
            $book->category_id = $data['category_id'];
        }
        if (isset($data['title'])) {
            $book->title = $data['title'];
        }
        if (array_key_exists('description', $data)) {
            $book->description = $data['description'];
        }
        if (isset($data['price'])) {
            $book->price = $data['price'];
        }
        if (isset($data['stock'])) {
            $book->stock = $data['stock'];
        }
        if (isset($data['status'])) {
            $book->status = $data['status'];
        }

        if ($request->hasFile('cover')) {
            // delete old cover if exists
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }

            $path = $request->file('cover')->store('covers', 'public');
            $book->cover_path = $path;
        }

        $book->save();

        return response()->json($book->load(['category', 'seller']));
    }

    // SELLER: delete own book
    public function destroy(Request $request, Book $book)
    {
        $user = $request->user();
        if ($user->role !== 'seller' || $book->seller_id !== $user->id) {
            return response()->json(['message' => 'You can only delete your own books'], 403);
        }

        $profile = $user->sellerProfile;
        if (! $profile || $profile->status !== 'approved') {
            return response()->json([
                'message' => 'Your seller account is not approved anymore. You cannot modify books.',
            ], 403);
        }

        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }

        $book->delete();

        return response()->json(null, 204);
    }
}
