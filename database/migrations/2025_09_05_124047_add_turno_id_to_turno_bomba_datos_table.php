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
        Schema::table('turno_bomba_datos', function (Blueprint $table) {
            // Agregar columna turno_id con referencia a la tabla turnos
            $table->foreignId('turno_id')->nullable()->after('user_id')->constrained('turnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turno_bomba_datos', function (Blueprint $table) {
            // Eliminar la columna turno_id
            $table->dropForeign(['turno_id']);
            $table->dropColumn('turno_id');
        });
    }
};
