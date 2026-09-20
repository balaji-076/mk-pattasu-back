<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mst_product_badges', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('color');
            $table->string('animation_type')->default('shimmer')->after('icon')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mst_product_badges', function (Blueprint $table) {
            $table->dropColumn(['icon', 'animation_type']);
        });
    }
};