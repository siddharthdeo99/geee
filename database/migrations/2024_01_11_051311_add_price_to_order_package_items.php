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
        Schema::table('order_package_items', function (Blueprint $table) {
            $table->unsignedInteger('price')->nullable();
            $table->foreignId('promotion_id')->nullable()->constrained('promotions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_package_items', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('promotion_id');
        });
    }
};
