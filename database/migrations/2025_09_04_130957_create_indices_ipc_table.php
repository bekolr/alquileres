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
        Schema::create('indices_ipc', function (Blueprint $table) {
            $table->id();
            $table->integer('anio');
            $table->integer('mes'); // 1 a 12
            $table->decimal('valor', 8, 6); // Ej: 1.025000 = +2.5%
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indices_ipc');
    }
};
