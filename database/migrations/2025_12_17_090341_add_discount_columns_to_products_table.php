<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('applied_discount', 5, 2)
                  ->default(0)
                  ->after('rate');

            $table->decimal('discount_rate', 10, 2)
                  ->nullable()
                  ->after('applied_discount');
        });

        /**
         * Apply discount to existing data
         * Example: applied_discount = 50%
         */
        DB::statement("
            UPDATE products
            SET 
                applied_discount = 50,
                discount_rate = rate - (rate * 50 / 100)
        ");
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['applied_discount', 'discount_rate']);
        });
    }
};
