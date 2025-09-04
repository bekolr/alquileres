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
        Schema::create('contrato_ajustes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->integer('desde_mes');          // 1 = primer mes del contrato
            $table->integer('duracion_meses');     // p.ej. 3
            $table->enum('tipo', ['IPC','PORCENTAJE']);
            $table->decimal('porcentaje', 8, 4)->nullable(); // si tipo=% fijo
            $table->boolean('cerrado')->default(false);
            $table->timestamps();

            $table->unique(['contrato_id','desde_mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_ajustes');
    }
};
