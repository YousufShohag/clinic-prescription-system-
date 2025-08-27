<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->text('oe')->nullable()->after('problem_description'); // On Examination notes
            $table->string('bp', 50)->nullable()->after('oe');            // e.g. 120/80 mmHg
            $table->integer('pulse')->nullable()->after('bp');            // bpm
            $table->decimal('temperature_c', 4, 1)->nullable()->after('pulse'); // e.g. 37.5
            $table->tinyInteger('spo2')->nullable()->after('temperature_c');    // %
            $table->integer('respiratory_rate')->nullable()->after('spo2');     // breaths/min
            $table->decimal('weight_kg', 5, 2)->nullable()->after('respiratory_rate');
            $table->decimal('height_cm', 5, 2)->nullable()->after('weight_kg');
            $table->decimal('bmi', 5, 2)->nullable()->after('height_cm');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn([
                'oe',
                'bp',
                'pulse',
                'temperature_c',
                'spo2',
                'respiratory_rate',
                'weight_kg',
                'height_cm',
                'bmi',
            ]);
        });
    }
};
