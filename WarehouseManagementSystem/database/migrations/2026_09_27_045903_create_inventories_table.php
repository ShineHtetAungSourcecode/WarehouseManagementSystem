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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(0); // Never negative; enforced in InventoryService
            $table->string('bin_location')->nullable(); // e.g., Aisle 4, Shelf B
            $table->timestamps();
            $table->unique(['warehouse_id', 'item_id']); // Unique tracking composite row per warehouse
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
