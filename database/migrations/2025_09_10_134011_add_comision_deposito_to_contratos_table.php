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
        Schema::table('contratos', function (Blueprint $table) {
        $table->boolean('tiene_comision')->default(0);
        $table->decimal('comision', 12, 2)->nullable();
        $table->unsignedInteger('comision_cuotas')->nullable();

        $table->boolean('tiene_deposito')->default(0);
        $table->decimal('deposito', 12, 2)->nullable();
        $table->unsignedInteger('deposito_cuotas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            //
        });
    }
};
