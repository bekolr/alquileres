<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;
use App\Models\Inquilino;
use App\Models\ContratoAjuste;
use App\Models\Departamento;
use App\Models\TipoAumento;
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
         $tiposAumento = TipoAumento::all();
        return view('contratos.create', [
            'inquilinos' => $inquilinos,
            'departamentos' => $departamentos,
            'tiposAumento' => $tiposAumento,
           
        ]);
    }
public function store(Request $request)
{
   
$data = $request->all();
    /*
    $data = $request->validate([
        'inquilino_id'            => ['required','exists:inquilinos,id'],
        'departamento_id'         => ['required','exists:departamentos,id'],
        'fecha_inicio'            => ['required','date'],
        'fecha_fin'               => ['nullable','date','after:fecha_inicio'],
        'dia_vencimiento'                => ['required','integer','between:1,28'], // ≤28 evita febrero
        'tasa_interes_diaria'          => ['nullable','numeric'],
        'monto_alquiler'  => ['required','numeric','min:0'],        // ej. 650000
        'incremento_cada_meses'         => ['required','integer','between:1,12'],// ej. 3 o 4
        'tiene_comision' => ['sometimes','boolean'], // si tenés esta columna
        'comision' => ['nullable','numeric','min:0'], // si tenés esta columna
        'comision_cuotas' => ['nullable','integer','min:1'], // si tenés esta columna
        'tiene_deposito' => ['sometimes','boolean'], // si tenés esta columna
        'deposito' => ['nullable','numeric','min:0'], // si tenés esta columna
        'deposito_cuotas' => ['nullable','integer','min:1'], // si tenés esta columna       
    ]);*/

    DB::transaction(function () use ($data) {

        $contrato = Contrato::create([
            'inquilino_id'    => $data['inquilino_id'],
            'departamento_id' => $data['departamento_id'],
            'fecha_inicio'    => $data['fecha_inicio'],
            'fecha_fin'       => $data['fecha_fin'] ?? null,
            'dia_vencimiento'        => $data['dia_vencimiento'],
            'tasa_interes_diaria'  => $data['interes_diario'] ?? 0,
            'monto_alquiler'   => $data['monto_alquiler'], 
            'incremento_cada_meses' => $data['incremento_cada_meses'],
            'tiene_comision' =>$data['tiene_comision'] ?? 0, // si tenés esta columna
            'comision' => $data['comision'] ?? null, // si tenés esta columna
            'comision_cuotas' =>$data['comision_cuotas'] ?? null, // si tenés esta columna
            'tiene_deposito' =>$data['tiene_deposito'] ?? 0, // si tenés esta columna
            'deposito' => $data['deposito'] ?? null, // si tenés esta columna
            'deposito_cuotas' => $data['deposito_cuotas'] ?? null,  
            'tipo_aumento' => $data['tipo_aumento'] ?? null, // si tenés esta columna
            
            // si tenés esta columna
        ]);
       /*crear cuotas iniciales*/
       $contrato->generarCuotas();
        return view('contratos.show', compact('contrato'));


    });

    return redirect()->route('contratos.index')
        ->with('ok','Contrato creado y cuotas iniciales generadas.');
}


    public function show(Contrato $contrato)
    {
        
         $contrato->load([
        'inquilino:id,nombre',
        'departamento' => fn($q) => $q->with('edificio:id,nombre,expensas')->select([ 'id','edificio_id','codigo']),
       
        'cuotas' => fn($q) => $q->orderBy('periodo'),

    ]);

   

    // Totales (soporta columnas nuevas; si alguna no existe, quedará 0 en la vista)
    $totales = [
        'alquiler' => $contrato->cuotas->sum('monto_alquiler'),
        'expensas' => $contrato->cuotas->sum('monto_expensas'),
        'comision' => $contrato->cuotas->sum('monto_comision'),
        'deposito' => $contrato->cuotas->sum('monto_deposito'),
        'total'    => $contrato->cuotas->sum('monto_total'),
        'pagado'   => $contrato->cuotas->sum('total_pagado'),
        'saldo'    => $contrato->cuotas->sum('saldo_con_interes'),
    ];

    return view('contratos.show', compact('contrato','totales'));
    }


}
