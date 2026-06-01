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
        Schema::create('pickups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('trip_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->foreignUlid('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->uuid('outlet_id')->nullable();
            $table->string('order_code')->nullable();
            $table->string('address_from');
            $table->string('address_to');
            $table->string('service_type')->default('Antar Jemput Standar');
            $table->uuid('employee_id')->nullable(); // driver/kurir
            $table->decimal('distance', 8, 2)->default(0.00);
            $table->string('eta')->default('30 menit');
            $table->integer('fee')->default(10000);
            $table->dateTime('scheduled_at');
            $table->string('weight')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['menunggu', 'jemput', 'proses', 'antar', 'selesai', 'batal'])->default('menunggu');
            $table->string('avatar_color')->default('#6366F1');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('outlet_id')
                ->references('id')
                ->on('outlets')
                ->onDelete('set null');

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickups');
    }
};
