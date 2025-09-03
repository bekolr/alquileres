<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use Illuminate\Http\Request;

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
        });

        return back()->with('ok','Pago registrado.');
    }
}
