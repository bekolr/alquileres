<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;
use App\Models\Inquilino;
use App\Models\ContratoAjuste;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\GeneradorCuotasPorBloqueService;

class ContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::with(['inquilino','departamento'])->latest()->paginate(20);
        return view('contratos.index', compact('contratos'));
    }

    public function create()
    {


         $departamentos = Departamento::with('edificio')->get();
         $inquilinos = Inquilino::orderBy('nombre')->get();
        return view('contratos.create', [
            'inquilinos' => $inquilinos,
            'departamentos' => $departamentos,
           
        ]);
    }
   public function store(Request $request, GeneradorCuotasPorBloqueService $svc)
    {
        // Validación
        $data = $request->validate([
            'inquilino_id'           => 'required|exists:inquilinos,id',
            'departamento_id'        => 'required|exists:departamentos,id',
            'fecha_inicio'           => 'required|date',
            'fecha_fin'              => 'required|date|after_or_equal:fecha_inicio',
            'dia_vencimiento'        => 'required|integer|min:1|max:28',
            'monto_alquiler'         => 'required|numeric|min:0',

            // Bloque inicial (opcional)
            'tipo_ajuste'            => 'nullable|in:IPC,PORCENTAJE',
            'incremento_cada_meses'  => 'nullable|integer|min:1',
            'porcentaje_incremento'  => 'nullable|numeric|min:0', // aceptamos 10 o 0.10

            // Interés (opcional)
            'tasa_interes_diaria'    => 'nullable|numeric|min:0',
        ]);

        // Fechas
        $fechaInicio = Carbon::parse($data['fecha_inicio'])->startOfDay();
        $fechaFin    = Carbon::parse($data['fecha_fin'])->endOfDay();

        // Meses de duración (incluye el mes de inicio)
        $duracionMeses = $fechaInicio->diffInMonths($fechaFin->copy()->addDay()) ?: 1;

        // Crear contrato
        $contrato = Contrato::create([
            'inquilino_id'    => $data['inquilino_id'],
            'departamento_id' => $data['departamento_id'],
            'monto_alquiler'  => $data['monto_alquiler'], // si tu columna se llama así
            'duracion_meses'  => $duracionMeses,
            'fecha_inicio'    => $fechaInicio,
            'fecha_fin'       => $fechaFin,
            'dia_vencimiento' => $data['dia_vencimiento'],
        ]);

        $tasaInteresDiaria = $data['tasa_interes_diaria'] ?? null;

        // ===== Bloque inicial opcional =====
        if (!empty($data['tipo_ajuste']) && !empty($data['incremento_cada_meses'])) {

            // Normalizar % fijo: permitir que el usuario ponga 10 (10%) o 0.10
            $porcentaje = null;
            if ($data['tipo_ajuste'] === 'PORCENTAJE') {
                $porcEntrada = (float) ($data['porcentaje_incremento'] ?? 0);
                // Si viene >= 1, lo pasamos a fracción (10 => 0.10)
                $porcentaje = $porcEntrada >= 1 ? ($porcEntrada / 100) : $porcEntrada;
            }

            // Crear el bloque por la relación (no hace falta pasar contrato_id)
            $bloque = $contrato->ajustes()->create([
                'desde_mes'      => 1,
                'duracion_meses' => (int) $data['incremento_cada_meses'],
                'tipo'           => $data['tipo_ajuste'],  // 'IPC' | 'PORCENTAJE'
                'porcentaje'     => 2.2,   
                   'cerrado'    => false // null si IPC
            ]);

            // Generar SOLO las cuotas del bloque
            $svc->generarParaBloque($bloque, [
                'dia_vencimiento'     => (int) $data['dia_vencimiento'],
                'tasa_interes_diaria' => $tasaInteresDiaria,
            ]);
        }

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('ok', 'Contrato creado. Se generaron las cuotas del primer bloque (si se configuró).');
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
