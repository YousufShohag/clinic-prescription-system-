<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->date('return_date')->nullable()->after('doctor_advice');
        });
    }
    public function down(): void {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('return_date');
        });
    }
};
