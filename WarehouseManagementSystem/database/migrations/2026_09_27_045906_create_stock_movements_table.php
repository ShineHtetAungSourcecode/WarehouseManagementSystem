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
       Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained(); // Captures the exact warehouse operator acting
            $table->integer('quantity_change'); // Positive integers for receiving, negative for picking
            $table->string('type'); // 'receive', 'pick', 'adjustment', 'transfer'
            $table->string('reference_type')->nullable(); // Polymorphic document logging tracking (e.g. App\Models\Order)
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
