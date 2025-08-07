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
            $table->enum('tipo', ['Super', 'Regular', 'Diesel', 'Otro'])->after('nombre');
            $table->decimal('precio', 8, 2)->after('tipo')->comment('Precio en quetzales');
            $table->decimal('galonaje', 10, 2)->after('precio')->comment('Cantidad en galones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bombas', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'precio', 'galonaje']);
        });
    }
};
