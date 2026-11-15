<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propuesta extends Model
{
    protected $fillable = ['candidato_id', 'tema', 'titulo', 'descripcion'];

    public function candidato()
    {
        return $this->belongsTo(Candidato::class);
    }
}
