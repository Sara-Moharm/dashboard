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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained("customers")->onDelete("set null");
            $table->foreignId('delivery_personnel_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('status', [
                'pending',
                // 'confirmed',
                'preparing',
                'ready',
                'delivering',
                'delivered',
                'cancelled',
                'failed'
            ])->default('pending');
            $table->timestamps();
            $table->timestamp('preparing_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivering_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
