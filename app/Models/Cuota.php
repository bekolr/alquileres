<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'contrato_id','periodo','vencimiento','monto_base','expensas',
        'interes_calculado','total_pagado','estado','observaciones',
        'monto_alquiler','monto_expensas','monto_comision','monto_deposito','monto_total'
    ];
    protected $casts = [
        'periodo' => 'date',
        'vencimiento' => 'date',
    ];

    public function contrato(){ return $this->belongsTo(Contrato::class); }
    public function pagos(){ return $this->hasMany(Pago::class); }

    public function getImporteBaseTotalAttribute(): float
    {
        return (float)$this->monto_base + (float)$this->expensas;
    }

    /** Interés simple diario hasta $fecha (default: hoy) */
    public function calcularInteresHasta(?Carbon $fecha = null): float
    {
        $fecha = $fecha ?: now();
        if ($fecha->lte($this->vencimiento)) return 0.0;

        $dias = $this->vencimiento->diffInDays($fecha);
        $tasa = (float)$this->contrato->tasa_interes_diaria; // ej 0.003 = 0.3%/día
        $saldoBase = max(0, $this->importe_base_total - (float)$this->total_pagado);

        return round($saldoBase * $tasa * $dias, 2);
    }

    public function getSaldoConInteresAttribute(): float
    {
        $interes = $this->calcularInteresHasta();
        return max(0, $this->importe_base_total + $interes - (float)$this->total_pagado);
    }

   

}
