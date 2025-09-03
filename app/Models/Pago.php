<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;
protected $fillable = ['cuota_id','fecha_pago','importe','medio','nota'];
    protected $casts = ['fecha_pago'=>'date'];

    public function cuota(){ return $this->belongsTo(Cuota::class); 
    }
    
}
