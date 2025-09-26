<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAumento extends Model
{
    use HasFactory;
    protected $fillable = ['nombre','descripcion'];
    protected $table = 'tipo_aumentos';
}
