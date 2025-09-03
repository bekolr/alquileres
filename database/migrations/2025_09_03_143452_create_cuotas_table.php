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
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained()->cascadeOnDelete();
        $table->date('periodo'); // usamos el día 1 del mes: ej 2025-09-01
        $table->date('vencimiento'); // dia_vencimiento de ese mes

        $table->decimal('monto_base', 12, 2);      // alquiler (con incrementos aplicados)
        $table->decimal('expensas', 12, 2)->default(0);
        $table->decimal('interes_calculado', 12, 2)->default(0); // snapshot al momento de pagar
        $table->decimal('total_pagado', 12, 2)->default(0);

        $table->enum('estado', ['pendiente','pagada','parcial'])->default('pendiente');
            $table->timestamps();
            $table->unique(['contrato_id','periodo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};
