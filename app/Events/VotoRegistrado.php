<?php

namespace App\Events;

use App\Models\Candidato;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VotoRegistrado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $resultados;

    public function __construct()
    {
        $this->resultados = Candidato::withCount('votos')->orderBy('votos_count', 'desc')->get();
    }

    public function broadcastOn(): array
    {
        return [new Channel('votos')];
    }
}
