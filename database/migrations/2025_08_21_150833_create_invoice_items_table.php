<?php

// database/migrations/xxxx_xx_xx_create_invoice_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // price at time of sale
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('invoice_items');
    }
};
