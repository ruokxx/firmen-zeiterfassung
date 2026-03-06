<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('unit')->default('Stück')->after('name');
            $table->decimal('stock_count', 10, 2)->default(0)->change();
            $table->decimal('low_stock_threshold', 10, 2)->default(2)->change();
        });

        Schema::table('material_transactions', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('unit');
            $table->integer('stock_count')->default(0)->change();
            $table->integer('low_stock_threshold')->default(2)->change();
        });

        Schema::table('material_transactions', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
