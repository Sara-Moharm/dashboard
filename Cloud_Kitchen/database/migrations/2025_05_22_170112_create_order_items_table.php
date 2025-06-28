<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->default(1);
            $table->float('price')->default(0.0);
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('category_dish_id')->nullable()->constrained('category_dishes')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->enum('status', [
                'pending',
                // 'confirmed',
                'preparing',
                'ready',
                // 'cancelled',
                'delivered'
            ])->default('pending');
            $table->timestamps();
            $table->timestamp('preparing_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
