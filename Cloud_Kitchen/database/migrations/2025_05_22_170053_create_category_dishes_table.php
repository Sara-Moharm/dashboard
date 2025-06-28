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
        Schema::create('category_dishes', function (Blueprint $table) {
            $table->id();
            $table->String('title',255)->unique();
            $table->text('description')->nullable();
            $table->float('price');
            $table->string('image_url')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            //$table->decimal('meal_rate', 3, 2)->nullable();
            $table->integer('availability')->nullable();
            $table->float('calories')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_dishes');
    }
};