<div class="space-y-4">
    <div class="bg-gray-50 p-4 rounded-lg border-l-4" style="border-color: {{ $propuesta->candidato->color_hex }}">
        <h3 class="font-bold text-lg">{{ $propuesta->candidato->nombre }} ({{ $propuesta->candidato->partido }})</h3>
        <p class="text-sm text-gray-600 mt-1"><strong>{{ $propuesta->titulo }}:</strong> {{ $propuesta->descripcion }}</p>
    </div>

    <h4 class="font-semibold text-gray-700 mt-6">Otros candidatos sobre "{{ $propuesta->tema }}":</h4>
    
    @php
        $otras = \App\Models\Propuesta::where('tema', $propuesta->tema)
            ->where('candidato_id', '!=', $propuesta->candidato_id)
            ->with('candidato')
            ->get();
    @endphp

    @if($otras->count() > 0)
        <div class="space-y-3">
            @foreach($otras as $otra)
                <div class="bg-white p-4 rounded-lg border-l-4" style="border-color: {{ $otra->candidato->color_hex }}">
                    <h5 class="font-bold">{{ $otra->candidato->nombre }} ({{ $otra->candidato->partido }})</h5>
                    <p class="text-sm text-gray-600 mt-1"><strong>{{ $otra->titulo }}:</strong> {{ $otra->descripcion }}</p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 italic">Ningún otro candidato ha hablado sobre este tema en sus entrevistas.</p>
    @endif
</div>
