<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_product_badges', function (Blueprint $table) {
            $table->id();
            $table->string('label', 50)->unique();        
            $table->string('value', 50)->unique();        
            $table->string('color', 30)->default('gray');  
            $table->unsignedSmallInteger('seq_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_badges');
    }
};