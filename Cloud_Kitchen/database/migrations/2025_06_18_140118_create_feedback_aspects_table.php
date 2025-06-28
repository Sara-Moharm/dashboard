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
        Schema::create('feedback_aspects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedback')->onDelete('cascade');
            $table->string('sentence'); // The sentence containing the aspect
            $table->string('aspect'); 
            $table->enum('sentiment', ['negative', 'neutral', 'positive', 'unprocessed'])->default('unprocessed'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_aspects');
    }
};
