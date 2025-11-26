<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // Seller = users.id with role 'seller'
            $table->foreignId('seller_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');

            // path to stored cover image (e.g. "covers/abc.jpg")
            $table->string('cover_path')->nullable();

            $table->timestamps();

            $table->index(['seller_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
