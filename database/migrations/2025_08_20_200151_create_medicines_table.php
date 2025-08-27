<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('medicines', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->onDelete('cascade'); 
        $table->string('name');
        $table->string('type'); 
        $table->integer('stock');
        $table->decimal('price', 8, 2);
        $table->date('expiry_date');
        $table->text('description')->nullable();
        $table->text('notes')->nullable(); // ✅ New field
        $table->string('image')->nullable();
        $table->boolean('status')->default(1); // Active/Inactive
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
