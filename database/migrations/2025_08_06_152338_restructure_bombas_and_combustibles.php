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
        // Añadir campos de galonaje por tipo de combustible a bombas
        Schema::table('bombas', function (Blueprint $table) {
            $table->decimal('galonaje_super', 10, 2)->default(0.00)->after('gasolinera_id');
            $table->decimal('galonaje_regular', 10, 2)->default(0.00)->after('galonaje_super');
            $table->decimal('galonaje_diesel', 10, 2)->default(0.00)->after('galonaje_regular');
            $table->decimal('galonaje_cc', 10, 2)->default(0.00)->after('galonaje_diesel');
            $table->enum('estado', ['activa', 'inactiva', 'mantenimiento'])->default('activa')->after('galonaje_cc');
        });

        // Eliminar la tabla combustibles ya que ahora los tipos están integrados en bombas
        // Pero primero verificamos si existen datos importantes
        Schema::dropIfExists('combustibles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear tabla combustibles
        Schema::create('combustibles', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->foreignId('bomba_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Remover campos de bombas
        Schema::table('bombas', function (Blueprint $table) {
            $table->dropColumn(['galonaje_super', 'galonaje_regular', 'galonaje_diesel', 'galonaje_cc', 'estado']);
        });
    }
};
