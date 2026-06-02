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
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('brand')->nullable();
            $table->string('category');
            $table->string('emoji', 10)->nullable();
            $table->string('color', 10)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(100);
            $table->string('unit')->default('pcs');
            $table->integer('price')->default(0);
            $table->foreignUuid('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->text('desc')->nullable();
            $table->date('last_restock')->nullable();
            $table->integer('last_restock_qty')->default(0);
            $table->json('history')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
