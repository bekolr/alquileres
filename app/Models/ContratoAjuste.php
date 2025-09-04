<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoAjuste extends Model
{
    use HasFactory;

    protected $fillable = [
        'contrato_id','desde_mes','duracion_meses','tipo','porcentaje','cerrado'
    ];

    public function contrato() { return $this->belongsTo(Contrato::class); }

    public function rangoMeses(): array
    {
        // ej: desde_mes=4, duracion=3 => [4,5,6]
        return range($this->desde_mes, $this->desde_mes + $this->duracion_meses - 1);
    }
}
