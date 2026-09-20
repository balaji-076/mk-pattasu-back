<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_offer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_offer_id')->constrained('combo_offers')
                  ->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')
                  ->cascadeOnDelete();
            $table->integer('quantity')->default(1);  
            $table->timestamps();

            $table->unique(['combo_offer_id', 'product_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_offer_items');
    }
};