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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Tenant isolated catalog items
            $table->string('sku'); // Stock Keeping Unit
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('price_cents')->default(0); // B2B contract pricing
            $table->unsignedInteger('reorder_level')->default(0); // Low-stock alert threshold
            $table->timestamps();
            $table->unique(['company_id', 'sku']); // Two companies may use the same SKU
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
