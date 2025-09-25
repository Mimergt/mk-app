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
            $table->decimal('resultado_cc', 10, 3)->default(0.000)->after('lectura_cc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turno_bomba_datos', function (Blueprint $table) {
            $table->dropColumn('resultado_cc');
        });
    }
};
