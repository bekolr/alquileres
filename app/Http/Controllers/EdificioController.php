<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use Illuminate\Http\Request;

class EdificioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $edificios = Edificio::all();
        return view('edificios.index', compact('edificios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('edificios.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
        ]);
        Edificio::create($request->all());
        return redirect()->route('edificios.index')->with('success', 'Edificio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Edificio $edificio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Edificio $edificio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Edificio $edificio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Edificio $edificio)
    {
        //
    }


    public function ajustarExpensasNoCobradas(Request $request, Edificio $edificio)
{
    $data = $request->validate([
        'tipo'               => 'required|in:fijo,porcentaje',
        'nuevo_valor'        => 'required_if:tipo,fijo|nullable|numeric|min:0',
        'porcentaje'         => 'required_if:tipo,porcentaje|nullable|numeric', // ej: 15 => +15%
        'aplicar_desde'      => 'required|date_format:Y-m',
        'actualizar_contratos' => 'sometimes|boolean', // opcional: también actualizar expensas_mensuales en contratos activos
    ],[
        'nuevo_valor.required_if' => 'Ingresá el nuevo valor cuando el tipo es fijo.',
        'porcentaje.required_if'  => 'Ingresá el porcentaje cuando el tipo es porcentaje.',
    ]);

    $desde  = \Carbon\Carbon::createFromFormat('Y-m', $data['aplicar_desde'])->startOfMonth();
    $factor = $data['tipo'] === 'porcentaje' ? (1 + ($data['porcentaje']/100)) : null;

    \DB::transaction(function () use ($edificio, $data, $desde, $factor) {

        // (Opcional) actualizar contratos para FUTURO
        if ($data['actualizar_contratos'] ?? false) {
            $qContratos = \App\Models\Contrato::where('estado','activo')
                ->whereHas('departamento', fn($d) => $d->where('edificio_id',$edificio->id));

            if ($data['tipo'] === 'fijo') {
                $qContratos->update(['expensas_mensuales' => $data['nuevo_valor']]);
            } else {
                $qContratos->chunkById(500, function($contratos) use ($factor){
                    foreach($contratos as $c){
                        $c->expensas_mensuales = round($c->expensas_mensuales * $factor, 2);
                        $c->save();
                    }
                });
            }
        }

        // AJUSTE solo a CUOTAS "pendiente" desde YYYY-MM
        $cuotasQ = \App\Models\Cuota::whereHas('contrato', function($q) use ($edificio){
                $q->where('estado','activo')
                  ->whereHas('departamento', fn($d)=>$d->where('edificio_id',$edificio->id));
            })
            ->whereDate('periodo', '>=', $desde)
            ->where('estado', 'pendiente') // **solo no cobradas**
            ->orderBy('id');

        $cuotasQ->chunkById(500, function($cuotas) use ($data, $factor){
            foreach ($cuotas as $cuota) {
                $nuevo = ($data['tipo'] === 'fijo')
                    ? (float)$data['nuevo_valor']
                    : round($cuota->expensas * $factor, 2);

                $dif = round($nuevo - (float)$cuota->expensas, 2);

                if (abs($dif) > 0.009) {
                    $cuota->ajustes()->create([
                        'concepto' => 'Ajuste expensas (edificio - solo pendientes)',
                        'importe'  => $dif,
                    ]);
                }
            }
        });
    });

    return back()->with('ok', 'Ajuste aplicado solo a cuotas no cobradas (pendientes).');
}

}
