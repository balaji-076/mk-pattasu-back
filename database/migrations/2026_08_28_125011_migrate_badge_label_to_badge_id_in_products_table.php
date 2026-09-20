<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add badge_id FK column (nullable — products without badge stay null)
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('badge_id')->nullable()->after('badge_label')
                  ->constrained('mst_product_badges')->nullOnDelete();
        });

        // 2. Backfill: match existing badge_label text with mst_product_badges.label
        DB::statement("
            UPDATE products p
            SET badge_id = b.id
            FROM mst_product_badges b
            WHERE LOWER(TRIM(p.badge_label)) = LOWER(TRIM(b.label))
        ");

        // 3. Drop old badge_label column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('badge_label');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('badge_label')->nullable()->after('badge_id');
        });

        DB::statement("
            UPDATE products p
            SET badge_label = b.label
            FROM mst_product_badges b
            WHERE p.badge_id = b.id
        ");

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('badge_id');
        });
    }
};