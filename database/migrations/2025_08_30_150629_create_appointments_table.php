<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Schema::create('appointments', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
        //     $table->timestamp('scheduled_at');
        //     $table->text('notes')->nullable();
        //     $table->timestamps();
        // });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->date('appointment_date');
            $table->timestamps();

            $table->unique(['doctor_id', 'appointment_date', 'patient_id']);
            $table->index(['doctor_id', 'appointment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};




