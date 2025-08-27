<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
{
    // Check if column does not exist using raw query
    $columnExists = DB::select("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'tests' 
        AND COLUMN_NAME = 'test_category_id'
    ");

    if (empty($columnExists)) {
        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('test_category_id')->nullable()->after('id');
        });
    }

    // Create default category if not exists
    $defaultId = DB::table('test_categories')->where('slug', 'general')->value('id');
    if (!$defaultId) {
        $defaultId = DB::table('test_categories')->insertGetId([
            'name'       => 'General',
            'slug'       => 'general',
            'description'=> 'Default category for existing tests',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Update old rows
    DB::table('tests')->whereNull('test_category_id')->update([
        'test_category_id' => $defaultId,
    ]);

    // Add foreign key if not exists
    Schema::table('tests', function (Blueprint $table) {
        if (!Schema::hasColumn('tests', 'test_category_id')) {
            $table->foreign('test_category_id')
                ->references('id')->on('test_categories')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        }
    });
}


    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            if (Schema::hasColumn('tests', 'test_category_id')) {
                $table->dropForeign(['test_category_id']);
                $table->dropColumn('test_category_id');
            }
        });
    }
};
