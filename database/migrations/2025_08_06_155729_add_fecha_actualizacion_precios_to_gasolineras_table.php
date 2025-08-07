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
        Schema::table('gasolineras', function (Blueprint $table) {
            $table->timestamp('fecha_actualizacion_precios')->nullable()->after('precio_cc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gasolineras', function (Blueprint $table) {
            $table->dropColumn('fecha_actualizacion_precios');
        });
    }
};
