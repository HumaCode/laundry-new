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
        Schema::create("outlets", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name");
            $table->string("code")->unique();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->string("city")->nullable();
            $table->string("manager")->nullable();
            $table->text("address")->nullable();
            $table->boolean("is_active")->default(true);
            $table->enum("payment_type", ["pay_first", "pay_later", "dp_first"])
                ->default("pay_later");

            // DP percentage (untuk dp_first)
            $table->unsignedTinyInteger("dp_percentage")
                ->default(50);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("outlets");
    }
};
