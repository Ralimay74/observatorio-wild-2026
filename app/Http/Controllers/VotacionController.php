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

        if (Voto::where('ip_address', $request->ip())->exists()) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Ya has emitido tu voto desde esta conexión.'
            ], 422);
        }

        Voto::create([
            'candidato_id' => $request->candidato_id,
            'ip_address' => $request->ip(),
        ]);

        $resultados = Candidato::withCount('votos')->orderBy('votos_count', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => '✅ ¡Voto registrado correctamente!',
            'resultados' => [
                'labels' => $resultados->pluck('nombre'),
                'data' => $resultados->pluck('votos_count'),
                'colors' => $resultados->pluck('color_hex'),
            ]
        ]);
    }

    public function resultados()
    {
        $r = Candidato::withCount('votos')->orderBy('votos_count', 'desc')->get();
        return response()->json([
            'labels' => $r->pluck('nombre'),
            'data' => $r->pluck('votos_count'),
            'colors' => $r->pluck('color_hex'),
        ]);
    }
}
