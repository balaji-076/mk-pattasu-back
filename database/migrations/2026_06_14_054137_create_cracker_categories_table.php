<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cracker_categories', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('value')->unique();
            $table->integer('seq_order')->default(0);
            $table->boolean('active_status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cracker_categories');
    }
};
