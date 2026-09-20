<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('hero_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('cta_link', 500)->nullable();
            $table->text('image_url');
            $table->string('image_public_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('seq_order')->default(0);
            $table->date('valid_from')->nullable();             
            $table->date('valid_to')->nullable();               
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('hero_sliders');
    }

};
