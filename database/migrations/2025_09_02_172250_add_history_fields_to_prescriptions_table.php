<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->text('ph')->nullable()->after('bmi');
            $table->text('dh')->nullable()->after('ph');
            $table->text('mh')->nullable()->after('dh');
            $table->text('oh')->nullable()->after('mh');
            $table->text('pae')->nullable()->after('oh');
            $table->text('dx')->nullable()->after('pae');
            $table->text('previous_investigation')->nullable()->after('dx');
            $table->text('ah')->nullable()->after('previous_investigation');
            $table->text('special_note')->nullable()->after('ah');
            $table->text('referred_to')->nullable()->after('special_note');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn([
                'ph',
                'dh',
                'mh',
                'oh',
                'pae',
                'dx',
                'previous_investigation',
                'ah',
                'special_note',
                'referred_to',
            ]);
        });
    }
};