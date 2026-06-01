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
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('service_code')->unique();
            $table->string('name');
            $table->string('emoji')->nullable();
            $table->string('category');
            $table->text('description')->nullable();
            $table->integer('price')->default(0);
            $table->string('unit')->default('/kg');
            $table->string('eta')->nullable();
            $table->string('color')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('express')->default(false);
            $table->boolean('pickup')->default(false);
            $table->integer('target')->default(100);
            $table->string('min_qty')->nullable();
            $table->json('features')->nullable();
            $table->json('tiers')->nullable();
            $table->integer('orders')->default(0);
            $table->bigInteger('revenue')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
