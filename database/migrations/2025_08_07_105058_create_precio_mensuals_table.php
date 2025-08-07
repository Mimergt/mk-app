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
        Schema::create('precios_mensuales', function (Blueprint $table) {
            $table->id();
            $table->integer('anio');
            $table->integer('mes');
            $table->decimal('super_compra', 8, 2)->default(0);
            $table->decimal('diesel_compra', 8, 2)->default(0);
            $table->decimal('regular_compra', 8, 2)->default(0);
            $table->decimal('super_venta', 8, 2)->default(0);
            $table->decimal('diesel_venta', 8, 2)->default(0);
            $table->decimal('regular_venta', 8, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['anio', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precio_mensuals');
    }
};
