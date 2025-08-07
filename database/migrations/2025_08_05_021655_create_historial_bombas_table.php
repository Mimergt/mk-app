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
        Schema::create('historial_bombas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bomba_id')->constrained('bombas')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('campo_modificado'); // 'precio' o 'galonaje'
            $table->decimal('valor_anterior', 10, 2);
            $table->decimal('valor_nuevo', 10, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_bombas');
    }
};
