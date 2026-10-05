<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained(); // Origin warehouse processing fulfillment
            $table->string('order_number');
            $table->string('status')->default('pending')->index(); // App\Enums\OrderStatus
            $table->timestamps();
            $table->unique(['company_id', 'order_number']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
