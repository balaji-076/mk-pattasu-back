<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // ── Visibility flags ─────────────────────────────────────────
            $table->boolean('is_featured')    ->default(false)->after('is_active');
            $table->boolean('is_trending')    ->default(false)->after('is_featured');
            $table->boolean('is_new_arrival') ->default(false)->after('is_trending');

            // null = no badge, or "Bestseller", "Limited", "Hot" etc. for admin badge management
            $table->string('badge_label', 50)->nullable()->after('is_new_arrival');

            // ── SEO fields ───────────────────────────────────────────────
            $table->string('slug')->nullable()->unique()->after('name');
            $table->text('meta_description')->nullable()->after('slug');
            
            // JSON array: ["crackers","diwali","sound"] — searchable tags
            $table->json('tags')->nullable()->after('meta_description');

            // ── Sort & display order (admin drag-reorder support) ────────
            $table->unsignedSmallInteger('sort_order')->default(0)->after('tags');

            // ── Soft deletes (admin trash instead of hard delete) ────────
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured',
                'is_trending',
                'is_new_arrival',
                'slug',
                'meta_description',
                'tags',
                'sort_order',
            ]);
            $table->dropSoftDeletes();
        });
    }
};