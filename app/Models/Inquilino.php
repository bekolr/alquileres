<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquilino extends Model
{
    use HasFactory;

     protected $fillable = ['user_id','dni','nombre','email','telefono'];

    public function user(){ return $this->belongsTo(User::class); }
    public function contratos(){ return $this->hasMany(Contrato::class); }
}
