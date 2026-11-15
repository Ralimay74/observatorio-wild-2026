<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrevista extends Model
{
    protected $fillable = ['candidato_id', 'titulo', 'youtube_url', 'fecha'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function candidato()
    {
        return $this->belongsTo(Candidato::class);
    }
}
