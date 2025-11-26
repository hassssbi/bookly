<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fantasy', 'description' => 'Features magic, mythical creatures, and imaginary worlds.'],
            ['name' => 'Romance', 'description' => 'Stories centered on romantic relationships.'],
            ['name' => 'Science Fiction', 'description' => 'Explores speculative technology, alternate realities, and scientific themes.'],
            ['name' => 'Mystery Thriller', 'description' => 'Involves solving a crime or a high-stakes suspenseful plot.'],
            ['name' => 'Horror', 'description' => 'Aims to evoke fear through supernatural or psychological means.'],
            ['name' => 'Historical Fiction', 'description' => 'Fictional stories set in the past.'],
            ['name' => 'Action Adventure', 'description' => 'Features a protagonist on a quest or a series of challenging events.'],
            ['name' => 'Western', 'description' => 'Stories set in the American West.'],
            ['name' => 'Biography', 'description' => "The story of a person's life, often a notable individual."],
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$counter++;
            }

            Category::firstOrCreate([
                'name' => $category['name'],
                'slug' => $slug,
                'description' => $category['description'] ?? null,
            ]);
        }
    }
}
