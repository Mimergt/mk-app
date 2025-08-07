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
        Schema::table('gastos_mensuales', function (Blueprint $table) {
            $table->json('gastos_adicionales')->nullable()->after('renta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastos_mensuales', function (Blueprint $table) {
            $table->dropColumn('gastos_adicionales');
        });
    }
};
