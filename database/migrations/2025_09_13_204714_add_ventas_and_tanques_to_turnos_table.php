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
        Schema::table('turnos', function (Blueprint $table) {
            // Campos para totales de ventas
            $table->decimal('venta_credito', 10, 2)->nullable();
            $table->decimal('venta_tarjetas', 10, 2)->nullable();
            $table->decimal('venta_efectivo', 10, 2)->nullable();
            $table->decimal('venta_descuentos', 10, 2)->nullable();

            // Campos para nivel de tanques en pulgadas
            $table->decimal('tanque_super_pulgadas', 8, 2)->nullable();
            $table->decimal('tanque_regular_pulgadas', 8, 2)->nullable();
            $table->decimal('tanque_diesel_pulgadas', 8, 2)->nullable();

            // Campos para nivel de tanques en galones
            $table->decimal('tanque_super_galones', 10, 2)->nullable();
            $table->decimal('tanque_regular_galones', 10, 2)->nullable();
            $table->decimal('tanque_diesel_galones', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropColumn([
                'venta_credito',
                'venta_tarjetas',
                'venta_efectivo',
                'venta_descuentos',
                'tanque_super_pulgadas',
                'tanque_regular_pulgadas',
                'tanque_diesel_pulgadas',
                'tanque_super_galones',
                'tanque_regular_galones',
                'tanque_diesel_galones'
            ]);
        });
    }
};
