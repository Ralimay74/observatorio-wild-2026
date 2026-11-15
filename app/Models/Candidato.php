<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    protected $fillable = ['nombre', 'partido', 'color_hex', 'resumen'];

    public function entrevista()
    {
        return $this->hasOne(Entrevista::class);
    }

    public function propuestas()
    {
        return $this->hasMany(Propuesta::class);
    }

    public function votos()
    {
        return $this->hasMany(Voto::class);
    }
}
