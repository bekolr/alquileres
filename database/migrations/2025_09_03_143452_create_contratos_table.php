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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
             $table->foreignId('inquilino_id')->constrained()->cascadeOnDelete();
        $table->foreignId('departamento_id')->constrained()->cascadeOnDelete();

        $table->date('fecha_inicio');
        $table->date('fecha_fin');             // inclusive
        $table->unsignedTinyInteger('dia_vencimiento')->default(10); // día de vencimiento mensual

        $table->decimal('monto_alquiler', 12, 2);
        $table->decimal('expensas_mensuales', 12, 2)->default(0);

        // interés simple diario por mora (ej: 0.003 = 0.3% diario)
        $table->decimal('tasa_interes_diaria', 8, 5)->default(0.003);

        // incrementos (opcional)
        $table->unsignedTinyInteger('incremento_cada_meses')->nullable(); // ej 6
        $table->decimal('porcentaje_incremento', 5, 2)->nullable();       // ej 20.00 (%)

        $table->enum('estado', ['activo','finalizado','rescindido'])->default('activo');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
