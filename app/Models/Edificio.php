<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edificio extends Model
{
    use HasFactory;

    
        protected $fillable = ['nombre','direccion'];

    public function departamentos(){ return $this->hasMany(Departamento::class); }

    // Acceso a contratos del edificio a través de los departamentos
    public function contratos()
    {
        return $this->hasManyThrough(
            Contrato::class, Departamento::class,
            'edificio_id',       // FK en departamentos -> edificios
            'departamento_id',   // FK en contratos -> departamentos
            'id',                // PK edificios
            'id'                 // PK departamentos
        );
    }


}
