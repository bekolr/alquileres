<?php

namespace App\Services;

use App\Models\Contrato;
use App\Models\ContratoAjuste;
use App\Models\Cuota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * Servicio para generar y recalcular cuotas por "bloques de ajuste".
 *
 * - Cada bloque define: desde_mes, duracion_meses, tipo (IPC | PORCENTAJE) y porcentaje (si aplica).
 * - Sólo genera cuotas de los meses cubiertos por el bloque.
 * - El alquiler del mes N se calcula partiendo del monto_inicial y aplicando
 *   en orden los factores de todos los bloques con desde_mes <= N.
 * - Expensas se toman del edificio del departamento del contrato.
 * - Se respeta dia_vencimiento (cap a 28).
 */
class GeneradorCuotasPorBloqueService
{
    /**
     * Genera/actualiza cuotas para el bloque especificado.
     *
     * @param  ContratoAjuste  $bloque
     * @param  array{dia_vencimiento?:int,tasa_interes_diaria?:float}  $opts
     *     - dia_vencimiento: día fijo del vencimiento (1..28). Si no viene, usa $contrato->dia_vencimiento o 1.
     *     - tasa_interes_diaria: si la manejás por cuota (nullable).
     */
    public function generarParaBloque(ContratoAjuste $bloque, array $opts = []): void
    {
        $contrato = $bloque->contrato()
            ->with(['ajustes', 'departamento.edificio'])
            ->firstOrFail();

        $diaVto      = (int)($opts['dia_vencimiento'] ?? $contrato->dia_vencimiento ?? 1);
        $diaVto      = max(1, min(28, $diaVto));
        $tasaDiaria  = $opts['tasa_interes_diaria'] ?? null;

        DB::transaction(function () use ($contrato, $bloque, $diaVto, $tasaDiaria) {
            foreach ($bloque->rangoMeses() as $nroMes) {

                // Vencimiento: día fijo del mes relativo al inicio del contrato
                $baseMes = $contrato->fecha_inicio
                    ->copy()
                    ->addMonthsNoOverflow($nroMes - 1)
                    ->startOfMonth();

                $vto = $baseMes->copy()->day($diaVto);

                $montoAlquiler = $this->calcularAlquilerMes($contrato, $nroMes);
                $montoExpensas = (float) $contrato->departamento->edificio->expensas;

                Cuota::updateOrCreate(
                    ['contrato_id' => $contrato->id, 'nro' => $nroMes],
                    [
                        'fecha_vencimiento' => $vto,
                        'monto_alquiler'    => $this->roundMoney($montoAlquiler),
                        'monto_expensas'    => $this->roundMoney($montoExpensas),
                        // Si tu tabla tiene este campo. Cambiá el nombre si difiere.
                        'interes_diario'    => $tasaDiaria,
                        'pagada'            => false,
                    ]
                );
            }
        });
    }

    /**
     * Recalcula alquiler/expensas de las cuotas NO pagadas del bloque.
     * No toca cuotas pagadas.
     */
    public function recalcularNoPagadasDelBloque(ContratoAjuste $bloque, array $opts = []): void
    {
        $contrato = $bloque->contrato()
            ->with(['ajustes', 'departamento.edificio', 'cuotas'])
            ->firstOrFail();

        $diaVto     = (int)($opts['dia_vencimiento'] ?? $contrato->dia_vencimiento ?? 1);
        $diaVto     = max(1, min(28, $diaVto));
        $tasaDiaria = $opts['tasa_interes_diaria'] ?? null;

        DB::transaction(function () use ($contrato, $bloque, $diaVto, $tasaDiaria) {
            foreach ($bloque->rangoMeses() as $nroMes) {
                $cuota = $contrato->cuotas()
                    ->where('nro', $nroMes)
                    ->where('pagada', false)
                    ->first();

                if (!$cuota) {
                    // Nada que recalcular si no existe o ya fue pagada
                    continue;
                }

                $baseMes = $contrato->fecha_inicio
                    ->copy()
                    ->addMonthsNoOverflow($nroMes - 1)
                    ->startOfMonth();
                $vto = $baseMes->copy()->day($diaVto);

                $nuevoAlq = $this->calcularAlquilerMes($contrato, $nroMes);
                $nuevoExp = (float) $contrato->departamento->edificio->expensas;

                $cuota->update([
                    'fecha_vencimiento' => $vto,
                    'monto_alquiler'    => $this->roundMoney($nuevoAlq),
                    'monto_expensas'    => $this->roundMoney($nuevoExp),
                    'interes_diario'    => $tasaDiaria ?? $cuota->interes_diario,
                ]);
            }
        });
    }

    /**
     * Calcula el alquiler del mes N, aplicando en cadena los factores
     * de todos los bloques con desde_mes <= N (en orden ascendente).
     */
    public function calcularAlquilerMes(Contrato $contrato, int $nroMes): float
    {
        $monto = (float) $contrato->monto_alquiler_inicial;

        // Trabajamos con la colección en memoria (ya eager-loaded en generar/recalcular)
        $bloques = $contrato->ajustes->sortBy('desde_mes');

        foreach ($bloques as $b) {
            if ($b->desde_mes > $nroMes) {
                break;
            }
            $monto = $this->aplicarAjusteDeBloque($contrato, $b, $monto);
        }

        return $monto;
    }

    /**
     * Aplica el ajuste de un bloque al monto actual.
     *
     * - PORCENTAJE: factor = 1 + porcentaje (ej: 0.12 -> +12%)
     * - IPC: factor = producto de IPC mensual de los meses calendario cubiertos por el bloque.
     */
    protected function aplicarAjusteDeBloque(Contrato $contrato, ContratoAjuste $b, float $montoActual): float
    {
        if ($b->tipo === 'PORCENTAJE') {
            $factor = 1.0 + (float) ($b->porcentaje ?? 0.0);
            return $montoActual * $factor;
        }

        // IPC: mapear meses de contrato a meses calendario reales del bloque
        $desdeCalendario = $contrato->fecha_inicio
            ->copy()
            ->addMonths($b->desde_mes - 1)
            ->startOfMonth();

        $hastaCalendario = $desdeCalendario
            ->copy()
            ->addMonths($b->duracion_meses - 1)
            ->endOfMonth();

        /** @var \App\Services\IpcService $ipcSvc */
        $ipcSvc = app(\App\Services\IpcService::class);
        $factor = $ipcSvc->factorAcumuladoMensual($desdeCalendario, $hastaCalendario);

        return $montoActual * $factor;
    }

    /**
     * Redondeo bancario 2 decimales.
     */
    protected function roundMoney(float $v): float
    {
        return round($v + 1e-8, 2, PHP_ROUND_HALF_UP);
    }
}
