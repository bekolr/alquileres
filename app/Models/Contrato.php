<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

     protected $fillable = [
        'inquilino_id','departamento_id','fecha_inicio','fecha_fin','dia_vencimiento',
        'monto_alquiler','tasa_interes_diaria','tipo_ajuste',
        'incremento_cada_meses','estado','expensas',
        'comision','comision_cuotas',
        'deposito','deposito_cuotas',   
        'observaciones','tipo_aumento'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function inquilino(){ return $this->belongsTo(Inquilino::class); }
        public function ajustes() { return $this->hasMany(ContratoAjuste::class)->orderBy('desde_mes'); }
    public function departamento(){ return $this->belongsTo(Departamento::class); }
    public function cuotas(){ return $this->hasMany(Cuota::class); }
   

    

public function generarCuotas(): void
{
    // Mes de inicio/fin del contrato
    $inicio = $this->fecha_inicio->copy()->startOfMonth();
    $fin    = $this->fecha_fin->copy()->startOfMonth();

    // N = tamaño del primer bloque (si no está seteado, 4)
    $n = (int) ($this->incremento_cada_meses ?: 4);

    // Expensas del edificio (ajustá el nombre del campo si es distinto)
    $expensasVigentes = (float) (optional(optional($this->departamento)->edificio)->expensas ?? 0.0);

    // Cuántos meses quedan en el contrato (tope por fecha_fin)
    $mesesContrato = $inicio->diffInMonths($fin) + 1;
    $aCrear = min($n, $mesesContrato);

    $mes = $inicio->copy();

    // --- Precalcular prorrateo de comisión y depósito con redondeo + ajuste de residuo ---
    $comisionTotal   = (float) ($this->comision ?? 0);
    $comisionCuotas  = (int)   ($this->comision_cuotas ?? 0);
    $depositoTotal   = (float) ($this->deposito ?? 0);
    $depositoCuotas  = (int)   ($this->deposito_cuotas ?? 0);

    $split = function (float $total, int $cuotas): array {
        if ($total <= 0 || $cuotas <= 0) return [];
        $base = $total / $cuotas;

        // Redondeamos cada parte a 2 decimales y luego ajustamos residuo en la primera
        $partes = array_fill(0, $cuotas, round($base, 2));
        $suma   = array_sum($partes);
        $res    = round($total - $suma, 2);
        if ($res !== 0.0) {
            $partes[0] = round($partes[0] + $res, 2);
        }
        return $partes;
    };

    $vectorComision = $split($comisionTotal, $comisionCuotas);
    $vectorDeposito = $split($depositoTotal, $depositoCuotas);

    for ($k = 0; $k < $aCrear; $k++) {

        // Alquiler base (sin incrementos de bloques; si luego aplicás IPC, lo harás en el siguiente bloque)
        $montoAlquiler  = (float) $this->monto_alquiler;

        // Expensas vigentes a la generación
        $montoExpensas  = (float) $expensasVigentes;

        // Comisión prorrateada (solo en las primeras N comision_cuotas)
        $montoComision  = ($vectorComision && $k < count($vectorComision)) ? (float) $vectorComision[$k] : 0.0;

        // Depósito prorrateado (solo en las primeras N deposito_cuotas)
        $montoDeposito  = ($vectorDeposito && $k < count($vectorDeposito)) ? (float) $vectorDeposito[$k] : 0.0;

        // Total discriminado
        $montoTotal     = round($montoAlquiler + $montoExpensas + $montoComision + $montoDeposito, 2);

        // Compatibilidad: "monto_base" = base sin comisiones ni depósito (alquiler + expensas)
        $montoBase      = round($montoAlquiler + $montoExpensas, 2);

        $periodo = $mes->copy(); // día 1 de ese mes
        $vto     = $mes->copy()->day(min((int) $this->dia_vencimiento, $mes->daysInMonth));

        // Evita duplicados por (contrato_id + periodo)
        Cuota::firstOrCreate(
            [
                'contrato_id' => $this->id,
                'periodo'     => $periodo->toDateString(),
            ],
            [
                'vencimiento'     => $vto,
                // Campos "históricos" que ya tenías:
                'monto_base'      => $montoBase,          // sin incrementos, sin comisión/deposito
                'expensas'        => $montoExpensas,      // si usás este campo legacy, lo mantenemos

                // Desglose nuevo (recomendado para la vista y reportes):
                'monto_alquiler'  => $montoAlquiler,
                'monto_expensas'  => $montoExpensas,
                'monto_comision'  => $montoComision,
                'monto_deposito'  => $montoDeposito,
                'monto_total'     => $montoTotal,
            ]
        );

        $mes->addMonthNoOverflow();
    }
}


}
