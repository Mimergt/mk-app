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
            // Añadir precios por tipo de combustible
            $table->decimal('precio_super', 8, 2)->default(0.00)->after('ubicacion');
            $table->decimal('precio_regular', 8, 2)->default(0.00)->after('precio_super');
            $table->decimal('precio_diesel', 8, 2)->default(0.00)->after('precio_regular');
            $table->decimal('precio_cc', 8, 2)->default(0.00)->after('precio_diesel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gasolineras', function (Blueprint $table) {
            $table->dropColumn(['precio_super', 'precio_regular', 'precio_diesel', 'precio_cc']);
        });
    }
};
