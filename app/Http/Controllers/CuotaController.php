<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CuotaController extends Controller
{

    public function show(Cuota $cuota)
    {
        $cuota->load('contrato.inquilino','pagos');

     
        return view('cuotas.show', compact('cuota'));
    }

    public function pagar(Request $request, Cuota $cuota)
    {
        $data = $request->validate([
            'fecha_pago' => 'required|date',
            'importe'    => 'required|numeric|min:0.01',
            'medio'      => 'nullable|string|max:50',
            'nota'       => 'nullable|string|max:500',
        ]);

        // snapshot de interés al momento del pago (visible para recibo)
        $interes = $cuota->calcularInteresHasta(\Carbon\Carbon::parse($data['fecha_pago']));

        DB::transaction(function() use ($cuota, $data, $interes) {
            $cuota->interes_calculado = $interes;
            $cuota->save();

            $cuota->pagos()->create($data);

            $pagado = (float)$cuota->pagos()->sum('importe');
            $totalConInteres = $cuota->importe_base_total + $interes;

            $cuota->total_pagado = $pagado;
            $cuota->estado = $pagado >= $totalConInteres - 0.01 ? 'pagada' : 'parcial';
            $cuota->save();

            //registrar movimiento
            $movimientoData = [
                'fecha' => $data['fecha_pago'],
                'tipo_movimiento'  => 'INGRESO',
                'concepto_id' => 1, //ajustar si es necesario
                'monto' => $data['importe'],
                'creado_por' => auth()->id(),  
                'metodo_pago' => $data['medio'] ?? null,
                'descripcion' => 'Pago de cuota ID '.$cuota->id.($data['nota'] ? (': '.$data['nota']) : ''),
                'referencia_type' => Cuota::class,
                'referencia_id'   => $cuota->id,
            ];
            \App\Models\Movimiento::create($movimientoData);

          
        });

        return back()->with('ok','Pago registrado.');
    }

     
    public function filtro(Request $request)
{
    $estado = $request->input('estado', 'pendiente'); // por defecto pendientes
    $desde  = $request->input('desde');
    $hasta  = $request->input('hasta');

    $query = Cuota::query();

  

    if ($estado !== 'todas') {
    if ($estado === 'pendiente') {
        $query->whereIn('estado', ['pendiente', 'parcial']);
    } else {
        $query->where('estado', $estado);
    }
}
    

    if ($desde && $hasta) {
        $query->whereBetween('vencimiento', [$desde, $hasta]);
    } elseif ($desde) {
        $query->whereDate('vencimiento', '>=', $desde);
    } elseif ($hasta) {
        $query->whereDate('vencimiento', '<=', $hasta);
    }

    $cuotas = $query->orderBy('vencimiento')->get();

    return view('cuotas.vencimientomes', compact('cuotas','estado','desde','hasta'));
}
  
}
