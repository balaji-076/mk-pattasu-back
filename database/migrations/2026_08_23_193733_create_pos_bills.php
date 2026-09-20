<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no')->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 15)->index();
            $table->string('customer_city')->nullable();
            $table->json('items');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('net_total', 10, 2);
            $table->string('payment_method', 50)->default('Cash');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_bills');
    }
};