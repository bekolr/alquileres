<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::with(['inquilino','departamento'])->latest()->paginate(20);
        return view('contratos.index', compact('contratos'));
    }

    public function create()
    {
        return view('contratos.create', [
            'inquilinos' => Inquilino::orderBy('nombre')->get(),
            'departamentos' => Departamento::orderBy('codigo')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'inquilino_id' => 'required|exists:inquilinos,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'dia_vencimiento' => 'required|integer|min:1|max:28',
            'monto_alquiler' => 'required|numeric|min:0',
            'expensas_mensuales' => 'nullable|numeric|min:0',
            'tasa_interes_diaria' => 'nullable|numeric|min:0',
            'incremento_cada_meses' => 'nullable|integer|min:1',
            'porcentaje_incremento' => 'nullable|numeric|min:0',
        ]);

        $contrato = Contrato::create($data);
        $contrato->generarCuotas();

        return redirect()->route('contratos.show', $contrato)->with('ok','Contrato creado y cuotas generadas.');
    }

    public function show(Contrato $contrato)
    {
        $contrato->load(['inquilino','departamento','cuotas'=>fn($q)=>$q->orderBy('periodo')]);
        return view('contratos.show', compact('contrato'));
    }
}
