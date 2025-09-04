<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;
use App\Models\Inquilino;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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


    public function actualizarExpensas(Request $request, Contrato $contrato)
{
    $data = $request->validate([
        'nuevas_expensas' => 'required|numeric|min:0',
        'aplicar_desde'   => 'required|date_format:Y-m',   // ej: 2025-10
        'modo'            => 'required|in:solo_pendientes,pendientes_y_parciales',
    ]);

    DB::transaction(function () use ($contrato, $data) {
        // guardá el nuevo valor en el contrato para futuras cuotas
        $contrato->expensas_mensuales = $data['nuevas_expensas'];
        $contrato->save();

        $desde = Carbon::createFromFormat('Y-m', $data['aplicar_desde'])->startOfMonth();

        $q = $contrato->cuotas()->whereDate('periodo', '>=', $desde);

        if ($data['modo'] === 'solo_pendientes') {
            $q->where('estado', 'pendiente');
        } else { // pendientes y parciales
            $q->whereIn('estado', ['pendiente','parcial']);
        }

        // Actualizar valor de expensas en las cuotas afectadas
        $q->update(['expensas' => $data['nuevas_expensas']]);
    });

    return back()->with('ok', 'Expensas actualizadas correctamente.');
}
}
