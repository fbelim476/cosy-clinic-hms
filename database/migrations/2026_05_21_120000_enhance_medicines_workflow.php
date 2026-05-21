<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->default(0)->after('selling_price');
            $table->string('medicine_type', 50)->nullable()->after('category');
            $table->string('strength', 50)->nullable()->after('medicine_type');
            $table->text('description')->nullable()->after('reorder_level');
        });

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->default(0)->after('quantity');
            $table->decimal('gst_percent', 5, 2)->default(0)->after('unit_price');
            $table->string('sku', 50)->nullable()->after('gst_percent');
        });

        Schema::table('pharmacy_order_items', function (Blueprint $table) {
            $table->string('sku', 50)->nullable()->after('medicine_name');
            $table->text('notes')->nullable()->after('is_otc');
        });
    }

    public function down(): void
    {
        Schema::table('pharmacy_order_items', function (Blueprint $table) {
            $table->dropColumn(['sku', 'notes']);
        });
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'gst_percent', 'sku']);
        });
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'medicine_type', 'strength', 'description']);
        });
    }
};
