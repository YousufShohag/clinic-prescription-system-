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
    Schema::table('invoices', function (Blueprint $table) {
        $table->enum('payment_method', ['Cash', 'Card', 'Mobile Banking', 'Due'])->default('Cash')->after('grand_total');
        $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_method');
    });
}

public function down()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn(['payment_method','paid_amount']);
    });
}
};
