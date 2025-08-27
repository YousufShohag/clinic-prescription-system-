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
        Schema::create('stock_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
    $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('type'); // "add", "remove", "rollback"
    $table->integer('quantity');
    $table->integer('stock_after'); // stock left after adjustment
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
