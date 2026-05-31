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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('outlet_id')->nullable();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('role');
            $table->boolean('is_active')->default(true);
            $table->text('address')->nullable();
            $table->date('joined_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('outlet_id')
                ->references('id')
                ->on('outlets')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
