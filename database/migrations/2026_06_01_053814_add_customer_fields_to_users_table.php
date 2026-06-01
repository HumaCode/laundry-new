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
        Schema::table('users', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('gender');
            $table->text('address')->nullable()->after('dob');
            $table->string('tier')->default('Baru')->after('address');
            $table->text('notes')->nullable()->after('tier');
            $table->uuid('outlet_id')->nullable()->after('notes');

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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn(['dob', 'address', 'tier', 'notes', 'outlet_id']);
        });
    }
};
