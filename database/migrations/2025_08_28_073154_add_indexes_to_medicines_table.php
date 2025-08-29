<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_indexes_to_medicines_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('medicines', function (Blueprint $table) {
            $table->index('status');
            $table->index('category_id');
            $table->index('name');
            $table->index('generic');
            $table->index('manufacturer');
            // Optional (if supported): $table->fullText(['name','generic','manufacturer','strength']);
        });
    }
    public function down(): void {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['name']);
            $table->dropIndex(['generic']);
            $table->dropIndex(['manufacturer']);
        });
    }
};
