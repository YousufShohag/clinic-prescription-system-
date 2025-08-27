<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialization')->nullable();
            $table->string('degree')->nullable();
            $table->string('bma_registration_number')->nullable();
            $table->string('chamber')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->decimal('consultation_fee', 8, 2)->nullable();
            $table->string('available_time')->nullable();
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
