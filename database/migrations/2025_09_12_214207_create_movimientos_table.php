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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
             
            // Fecha y tipo (ingreso / egreso)
            $table->date('fecha');
            $table->string('tipo_movimiento'); // ingreso, egreso

          

            // Relación polimórfica opcional (ej: cuota, contrato, factura...)
            $table->nullableMorphs('referencia'); 
            // crea: referencia_id (bigint), referencia_type (string)

            // Datos del movimiento
            $table->decimal('monto', 12, 2);
            $table->string('metodo_pago')->nullable(); // efectivo, transferencia, etc.
            $table->text('descripcion')->nullable();

            // Usuario que creó el movimiento
            $table->foreignId('creado_por')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
