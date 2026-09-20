<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')
                  ->cascadeOnDelete();
            $table->enum('status', [
                'placed',
                'confirmed',
                'packed',
                'shipped',
                'delivered',
                'cancelled'
            ]);
            $table->string('note')->nullable(); 
            $table->foreignId('changed_by')->nullable()
                  ->constrained('users')->nullOnDelete(); 
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};