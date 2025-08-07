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
            $table->boolean('cc_activo')->default(true)->after('precio_cc')
                ->comment('Indica si la gasolinera maneja combustible CC');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gasolineras', function (Blueprint $table) {
            $table->dropColumn('cc_activo');
        });
    }
};
