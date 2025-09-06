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
            // Hacer turno_identificador nullable ya que ahora usamos turno_id
            $table->string('turno_identificador')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turno_bomba_datos', function (Blueprint $table) {
            // Revertir: hacer turno_identificador requerido
            $table->string('turno_identificador')->nullable(false)->change();
        });
    }
};
