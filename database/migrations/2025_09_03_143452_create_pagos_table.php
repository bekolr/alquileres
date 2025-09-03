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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
             $table->foreignId('cuota_id')->constrained()->cascadeOnDelete();
        $table->date('fecha_pago');
        $table->decimal('importe', 12, 2);
        $table->string('medio')->nullable(); // ej 'efectivo', 'transferencia'
        $table->text('nota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
