<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    protected $fillable = ['candidato_id', 'ip_address'];

    public function candidato()
    {
        return $this->belongsTo(Candidato::class);
    }
}
