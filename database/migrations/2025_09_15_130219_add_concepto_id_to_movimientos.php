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
        Schema::table('movimientos', function (Blueprint $table) {
            $table->unsignedBigInteger('concepto_id')->nullable()->after('tipo_movimiento');
            $table->foreign('concepto_id')->references('id')->on('conceptos')->
            onDelete('set null');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            //
            $table->dropForeign(['concepto_id']);
            $table->dropColumn('concepto_id');
        });
    }
};
