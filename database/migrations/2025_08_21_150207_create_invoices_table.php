<?php

// database/migrations/xxxx_xx_xx_create_invoices_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('invoice_date')->default(now());
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('status')->default(1); // 1=Paid, 0=Unpaid
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('invoices');
    }
};
