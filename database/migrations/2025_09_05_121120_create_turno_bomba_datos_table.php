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
        Schema::create('turno_bomba_datos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bomba_id')->constrained('bombas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('turno_identificador'); // Identificador único del turno
            $table->decimal('galonaje_super', 10, 3)->default(0);
            $table->decimal('galonaje_regular', 10, 3)->default(0);
            $table->decimal('galonaje_diesel', 10, 3)->default(0);
            $table->decimal('lectura_cc', 10, 3)->default(0);
            $table->string('fotografia')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_turno');
            $table->timestamps();
            
            // Índices para consultas eficientes
            $table->index(['bomba_id', 'turno_identificador']);
            $table->index(['fecha_turno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turno_bomba_datos');
    }
};
