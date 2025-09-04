<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;
    protected $fillable = ['codigo','piso','descripcion'];
    public function contratos(){ return $this->hasMany(Contrato::class); }
    public function edificio(){ return $this->belongsTo(Edificio::class); }


}
