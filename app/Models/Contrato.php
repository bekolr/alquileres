<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

     protected $fillable = [
        'inquilino_id','departamento_id','fecha_inicio','fecha_fin','dia_vencimiento',
        'monto_alquiler','expensas_mensuales','tasa_interes_diaria',
        'incremento_cada_meses','porcentaje_incremento','estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function inquilino(){ return $this->belongsTo(Inquilino::class); }
    public function departamento(){ return $this->belongsTo(Departamento::class); }
    public function cuotas(){ return $this->hasMany(Cuota::class); }

    /** Genera cuotas mensuales desde fecha_inicio a fecha_fin */
    public function generarCuotas(): void
    {
        $inicio = $this->fecha_inicio->copy()->startOfMonth();
        $fin    = $this->fecha_fin->copy()->startOfMonth();

        $mes = $inicio->copy();
        $i = 0; // contador de meses para incrementos

        while ($mes <= $fin) {
            $i++;

            // calcular alquiler con incrementos (si aplica)
            $montoAlquiler = $this->monto_alquiler;
            if ($this->incremento_cada_meses && $this->porcentaje_incremento) {
                $bloques = intdiv(max($i-1,0), $this->incremento_cada_meses);
                if ($bloques > 0) {
                    $montoAlquiler = $montoAlquiler * pow(1 + ($this->porcentaje_incremento/100), $bloques);
                }
            }

            $periodo = $mes->copy(); // día 1 de ese mes
            $vto     = $mes->copy()->day(min($this->dia_vencimiento, $mes->daysInMonth));

            Cuota::firstOrCreate([
                'contrato_id' => $this->id,
                'periodo'     => $periodo->toDateString(),
            ],[
                'vencimiento' => $vto,
                'monto_base'  => round($montoAlquiler, 2),
                'expensas'    => $this->expensas_mensuales,
            ]);

            $mes->addMonthNoOverflow();
        }
    }
}
