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
        Schema::table('gasolineras', function (Blueprint $table) {
            // Remover campos relacionados con precios de CC
            $table->dropColumn(['precio_cc', 'cc_activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gasolineras', function (Blueprint $table) {
            // Restaurar campos en caso de rollback
            $table->decimal('precio_cc', 8, 2)->default(0.00);
            $table->boolean('cc_activo')->default(true);
        });
    }
};
