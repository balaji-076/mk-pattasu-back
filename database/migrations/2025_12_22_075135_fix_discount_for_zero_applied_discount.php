<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE products
            SET
                applied_discount = CASE
                    WHEN applied_discount IS NULL OR applied_discount = 0
                    THEN 50
                    ELSE applied_discount
                END,
                discount_rate = ROUND(
                    rate - (rate * 
                        CASE
                            WHEN applied_discount IS NULL OR applied_discount = 0
                            THEN 50
                            ELSE applied_discount
                        END
                    / 100),
                    2
                )
            WHERE rate IS NOT NULL
        ");
    }

    public function down(): void
    {
        // data rollback not required
    }
};
