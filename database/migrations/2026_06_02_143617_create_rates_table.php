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
        Schema::create('rates', function (Blueprint $table) {
              $table->id();

            $table->string('title')->nullable();
            $table->string('description', 80)->nullable();

            $table->integer('rating')->nullable();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->foreignId('product_id')
                ->constrained('products');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
