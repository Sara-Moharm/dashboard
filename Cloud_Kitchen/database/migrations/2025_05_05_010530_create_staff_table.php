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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // FK to users table
            $table->timestamp('shift_start')->nullable();  // Nullable for non-kitchen staff
            $table->timestamp('shift_end')->nullable();    // Nullable for non-kitchen staff
            $table->enum('status', ['available', 'busy'])->nullable(); // Nullable for non-kitchen staff
            $table->timestamps();
            $table->softDeletes();  // Soft delete for staff records
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
    
};