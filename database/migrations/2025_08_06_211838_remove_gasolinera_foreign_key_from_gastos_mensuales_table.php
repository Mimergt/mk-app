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
        Schema::table('gastos_mensuales', function (Blueprint $table) {
            // Eliminar la clave foránea
            $table->dropForeign(['gasolinera_id']);
            // Eliminar la columna gasolinera_id
            $table->dropColumn('gasolinera_id');
            // Modificar el índice único para que solo sea por año y mes
            $table->dropUnique(['gasolinera_id', 'anio', 'mes']);
            $table->unique(['anio', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastos_mensuales', function (Blueprint $table) {
            // Restaurar la columna gasolinera_id
            $table->unsignedBigInteger('gasolinera_id')->after('id');
            // Restaurar la clave foránea
            $table->foreign('gasolinera_id')->references('id')->on('gasolineras')->onDelete('cascade');
            // Restaurar el índice único original
            $table->dropUnique(['anio', 'mes']);
            $table->unique(['gasolinera_id', 'anio', 'mes']);
        });
    }
};
