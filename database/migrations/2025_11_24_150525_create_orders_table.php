<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Buyer = users.id (role usually 'customer')
            $table->foreignId('buyer_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->decimal('total_amount', 10, 2);
            $table->enum('status', [
                'pending',
                'paid',
                'shipped',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->string('payment_method')->nullable();

            $table->timestamps();

            $table->index(['buyer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
