<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('shipping_address')->after('customer_id');
            $table->string('shipping_city')->after('shipping_address');
            $table->string('shipping_state')->after('shipping_city');
            $table->string('shipping_pincode')->after('shipping_state');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['address', 'city', 'state', 'pincode']);
        });
    }
};
