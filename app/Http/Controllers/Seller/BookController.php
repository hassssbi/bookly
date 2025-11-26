<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    // List seller's own books with basic search/filter
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Book::where('seller_id', $user->id)->with('category');

        if ($search = $request->input('q')) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $books = $query
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('seller.books.index', compact('books', 'categories'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        // Ensure seller is approved
        $profile = $user->sellerProfile;
        if (! $profile || $profile->status !== 'approved') {
            return redirect()
                ->route('seller.profile.show') // or wherever your profile page is
                ->with('status', 'Your seller profile is not approved yet. Please complete your profile and wait for admin approval.');
        }

        $categories = Category::orderBy('name')->get();

        return view('seller.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Ensure seller is approved
        $profile = $user->sellerProfile;
        if (! $profile || $profile->status !== 'approved') {
            return redirect()
                ->route('seller.books.index')
                ->with('status', 'Your seller profile is not approved yet. You cannot create books.');
        }

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'cover' => 'nullable|image|max:2048', // 2MB
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
        $book->status = $data['status'];

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $book->cover_path = $path;
        }

        $book->save();

        return redirect()
            ->route('seller.books.index')
            ->with('status', 'Book created successfully.');
    }

    public function edit(Request $request, Book $book)
    {
        $user = $request->user();

        if ($book->seller_id !== $user->id) {
            abort(403);
        }

        $categories = Category::orderBy('name')->get();

        return view('seller.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $user = $request->user();

        if ($book->seller_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'cover' => 'nullable|image|max:2048',
        ]);

        if ($data['title'] !== $book->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Book::where('slug', $slug)->where('id', '!=', $book->id)->exists()) {
                $slug = $originalSlug.'-'.$counter++;
            }
            $book->slug = $slug;
        }

        $book->category_id = $data['category_id'];
        $book->title = $data['title'];
        $book->description = $data['description'] ?? null;
        $book->price = $data['price'];
        $book->stock = $data['stock'];
        $book->status = $data['status'];

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }

            $path = $request->file('cover')->store('covers', 'public');
            $book->cover_path = $path;
        }

        $book->save();

        return redirect()
            ->route('seller.books.index')
            ->with('status', 'Book updated successfully.');
    }

    public function destroy(Request $request, Book $book)
    {
        $user = $request->user();

        if ($book->seller_id !== $user->id) {
            abort(403);
        }

        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }

        $book->delete();

        return redirect()
            ->route('seller.books.index')
            ->with('status', 'Book deleted.');
    }

    // “Customer-style” view for seller (hero product view)
    public function show(Request $request, Book $book)
    {
        $user = $request->user();

        if ($book->seller_id !== $user->id) {
            abort(403);
        }

        $book->load('category');

        return view('seller.books.show', compact('book'));
    }
}
