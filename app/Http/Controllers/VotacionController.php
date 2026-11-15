<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use App\Models\Voto;
use Illuminate\Http\Request;

class VotacionController extends Controller
{
    public function index()
    {
        $candidatos = Candidato::with('entrevista', 'propuestas')->get();
        $resultados = Candidato::withCount('votos')->orderBy('votos_count', 'desc')->get();
        
        return view('votacion.index', compact('candidatos', 'resultados'));
    }

    public function votar(Request $request)
    {
        $request->validate(['candidato_id' => 'required|exists:candidatos,id']);
        $ip = $request->ip();

        if (Voto::where('ip_address', $ip)->exists()) {
            return back()->with('error', '⚠️ Ya has emitido tu voto desde esta conexión.');
        }

        Voto::create([
            'candidato_id' => $request->candidato_id,
            'ip_address' => $ip,
        ]);

        return back()->with('success', '✅ ¡Voto registrado correctamente!');
    }
}
