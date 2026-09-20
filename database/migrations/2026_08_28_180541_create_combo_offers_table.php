<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('image_public_id')->nullable();

            // Pricing
            $table->decimal('original_total', 10, 2)->default(0); 
            $table->decimal('combo_price', 10, 2);                
            $table->decimal('discount_percent', 5, 2)->default(0);

            // Validity
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_offers');
    }
};