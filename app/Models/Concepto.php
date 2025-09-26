<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'tipo_movimiento']; // 'INGRESO', 'EGRESO', 'MIXTO'
    protected $table = 'conceptos';
}
