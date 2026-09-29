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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Isolates roles to each B2B client
            $table->string('name'); // Tenant Admin, Warehouse Manager, Picker
            $table->string('slug'); // tenant-admin, warehouse-manager, picker
            $table->timestamps();
            $table->unique(['company_id', 'slug']); // Enforces role scope uniqueness within a company
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
