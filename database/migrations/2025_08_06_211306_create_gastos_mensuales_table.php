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
        Schema::create('gastos_mensuales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gasolinera_id');
            $table->integer('anio'); // 2024, 2025, etc.
            $table->integer('mes'); // 1-12
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('servicios', 10, 2)->default(0);
            $table->decimal('planilla', 10, 2)->default(0);
            $table->decimal('renta', 10, 2)->default(0);
            $table->timestamps();
            
            // Índice único para evitar duplicados por gasolinera/año/mes
            $table->unique(['gasolinera_id', 'anio', 'mes']);
            
            // Relación con gasolineras
            $table->foreign('gasolinera_id')->references('id')->on('gasolineras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos_mensuales');
    }
};
