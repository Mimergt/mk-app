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
        Schema::table('turnos', function (Blueprint $table) {
            $table->decimal('precio_super', 8, 4)->nullable()->after('venta_descuentos');
            $table->decimal('precio_regular', 8, 4)->nullable()->after('precio_super');
            $table->decimal('precio_diesel', 8, 4)->nullable()->after('precio_regular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropColumn(['precio_super', 'precio_regular', 'precio_diesel']);
        });
    }
};
