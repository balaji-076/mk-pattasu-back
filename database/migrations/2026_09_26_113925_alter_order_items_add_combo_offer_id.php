<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();

            $table->foreignId('combo_offer_id')->nullable()->after('product_id')
                ->constrained('combo_offers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['combo_offer_id']);
            $table->dropColumn('combo_offer_id');

            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};