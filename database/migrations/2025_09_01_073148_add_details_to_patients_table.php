<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->unsignedTinyInteger('age')->nullable()->after('doctor_id');
            $table->enum('sex', ['male', 'female', 'others'])->nullable()->after('age');
            $table->date('dob')->nullable()->after('sex');
            $table->string('address')->nullable()->after('dob');
            $table->json('images')->nullable()->after('address');
            $table->json('documents')->nullable()->after('images');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('documents');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['age', 'sex', 'dob', 'address', 'images', 'documents', 'status']);
        });
    }
};