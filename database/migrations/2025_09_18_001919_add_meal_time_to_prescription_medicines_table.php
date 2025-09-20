<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('prescription_medicines', function (Blueprint $table) {
            // keep it short; we’ll validate allowed values in the request
            $table->string('meal_time', 20)->nullable()->after('times_per_day')->index();
        });
    }

    public function down(): void
    {
        Schema::table('prescription_medicines', function (Blueprint $table) {
            $table->dropColumn('meal_time');
        });
    }
};
