<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('patients', function (Blueprint $table) {
            $table->date('next_return_date')->nullable()->after('email');
        });
    }
    public function down(): void {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('next_return_date');
        });
    }
};