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
        Schema::table('bombas', function (Blueprint $table) {
            // Eliminar campos obsoletos
            $table->dropColumn(['tipo', 'precio', 'galonaje']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bombas', function (Blueprint $table) {
            // Restaurar campos en caso de rollback
            $table->string('tipo');
            $table->decimal('precio', 8, 2);
            $table->decimal('galonaje', 10, 2);
        });
    }
};
