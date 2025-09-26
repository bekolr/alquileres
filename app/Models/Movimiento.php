<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;


      protected $fillable = [
        'fecha', 'tipo_movimiento', 'concepto_id',
        'monto', 'descripcion', 'metodo_pago', 'creado_por'
    ];

    public function concepto()
    {
        return $this->belongsTo(Concepto::class);
    }

    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
