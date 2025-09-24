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
            $table->decimal('galones_vendidos_super', 10, 3)->default(0.000)->after('galonaje_diesel');
            $table->decimal('galones_vendidos_regular', 10, 3)->default(0.000)->after('galones_vendidos_super');
            $table->decimal('galones_vendidos_diesel', 10, 3)->default(0.000)->after('galones_vendidos_regular');
            $table->decimal('ventas_galones_super', 10, 2)->default(0.00)->after('galones_vendidos_diesel');
            $table->decimal('ventas_galones_regular', 10, 2)->default(0.00)->after('ventas_galones_super');
            $table->decimal('ventas_galones_diesel', 10, 2)->default(0.00)->after('ventas_galones_regular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turno_bomba_datos', function (Blueprint $table) {
            $table->dropColumn([
                'galones_vendidos_super',
                'galones_vendidos_regular',
                'galones_vendidos_diesel',
                'ventas_galones_super',
                'ventas_galones_regular',
                'ventas_galones_diesel'
            ]);
        });
    }
};
