<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // PUBLIC: list all categories (optionally paginated)
    public function index()
    {
        return Category::orderBy('name')->get();
        // if you prefer pagination:
        // return Category::orderBy('name')->paginate(20);
    }

    // ADMIN: create category
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($data['name']);

        // ensure slug unique
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter++;
        }

        $category = Category::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);

        return response()->json($category, 201);
    }

    // Not used in routes (but keeping for completeness)
    public function show(Category $category)
    {
        return $category;
    }

    // ADMIN: update category
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string',
        ]);

        if (isset($data['name']) && $data['name'] !== $category->name) {
            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug.'-'.$counter++;
            }
            $data['slug'] = $slug;
        }

        $category->update($data);

        return response()->json($category);
    }

    // ADMIN: delete category
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(null, 204);
    }
}
