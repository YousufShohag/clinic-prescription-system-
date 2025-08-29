<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('generic')->after('name');
            $table->string('strength')->nullable()->after('generic');       // e.g. "500 mg", "5 mg/5 mL"
            $table->string('manufacturer')->nullable()->after('strength');  // keep nullable if not always known
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['generic', 'strength', 'manufacturer']);
        });
    }
};
