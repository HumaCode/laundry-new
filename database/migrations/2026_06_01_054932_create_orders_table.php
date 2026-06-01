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
            $table->uuid('id')->primary();
            $table->string('order_code')->unique();
            $table->foreignUlid('customer_id')->constrained('users')->onDelete('cascade');
            $table->uuid('outlet_id');
            $table->string('service_type');
            $table->decimal('weight', 8, 2)->default(0.00);
            $table->integer('price_per_unit')->default(0);
            $table->integer('total_price')->default(0);
            $table->enum('order_status', ['Baru', 'Proses', 'Selesai', 'Diambil'])->default('Baru');
            $table->enum('payment_status', ['Belum', 'Lunas'])->default('Belum');
            $table->string('payment_method')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('outlet_id')
                ->references('id')
                ->on('outlets')
                ->onDelete('cascade');
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
