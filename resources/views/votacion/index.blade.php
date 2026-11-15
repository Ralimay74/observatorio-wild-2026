<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observatorio Wild 2026 - Sondeo Informativo</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f59e0b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Wild 2026">
    <link rel="apple-touch-icon" href="/icon.svg">
    <link rel="icon" type="image/svg+xml" href="/icon.svg">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #portada { transition: transform .8s ease, opacity .8s ease; }
        .fade-out { transform: translateY(-100%); opacity: 0; pointer-events: none; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- ===== PORTADA ===== -->
    <div id="portada" class="fixed inset-0 z-50 flex items-center justify-center"
         style="background: linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#312e81 100%);">
        <div class="text-center px-6">
            <p class="text-amber-400 tracking-widest text-sm font-semibold mb-4">THE WILD PROJECT · NOVIEMBRE 2026</p>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4">🗳️ Observatorio Wild</h1>
            <p class="text-slate-300 text-lg max-w-xl mx-auto mb-10">
                Sondeo informativo sobre las entrevistas a los candidatos a las Elecciones Generales 2026.
                Vídeo, propuestas comparadas y estimación de voto.
            </p>
            <button onclick="entrar()"
                class="px-10 py-4 bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold text-xl rounded-full shadow-2xl transition transform hover:scale-105">
                ▶ ENTRAR
            </button>
            <p class="text-slate-500 text-xs mt-8">Medio de estudio informativo · Sin fines electorales oficiales</p>
        </div>
    </div>

    <!-- ===== BOTÓN DE SILENCIO ===== -->
    <button id="btnMute" onclick="toggleMute()" class="fixed bottom-4 right-4 z-40 bg-slate-800 text-white w-12 h-12 rounded-full shadow-lg hidden">
        🔊
    </button>

    <!-- ===== APLICACIÓN ===== -->
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-2">🗳️ Sondeo Informativo: Elecciones 2026</h1>
        <p class="text-center text-gray-600 mb-8">Entrevistas de Jordi Wild - The Wild Project</p>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div class="grid gap-6 mb-10">
            @foreach($candidatos as $c)
                <div class="bg-white p-6 rounded-lg shadow border-l-4" style="border-color: {{ $c->color_hex }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold">{{ $c->nombre }} <span class="text-sm font-normal text-gray-500">({{ $c->partido }})</span></h2>
                            <p class="text-gray-600 mt-2">{{ $c->resumen }}</p>
                        </div>
                        <form action="{{ route('votar') }}" method="POST" class="flex flex-col items-end gap-2 ml-4">
                            @csrf
                            <input type="hidden" name="candidato_id" value="{{ $c->id }}">
                            <button type="submit" class="px-6 py-3 text-white font-bold rounded hover:opacity-90 transition shadow-lg" style="background-color: {{ $c->color_hex }}">
                                🗳️ Votar
                            </button>
                        </form>
                    </div>

                    @if($c->propuestas->count() > 0)
                        <div class="mt-4 bg-gray-50 p-4 rounded">
                            <h3 class="font-semibold text-gray-700 mb-3">📋 Propuestas clave:</h3>
                            <div class="grid gap-2">
                                @foreach($c->propuestas as $p)
                                    <div class="bg-white p-3 rounded border-l-2" style="border-color: {{ $c->color_hex }}">
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-2">{{ $p->tema }}</span>
                                        <strong class="text-gray-800">{{ $p->titulo }}:</strong>
                                        <span class="text-gray-600 text-sm">{{ $p->descripcion }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($c->entrevista)
                        <div class="mt-4">
                            <h3 class="font-semibold text-gray-700 mb-2">🎥 Entrevista completa:</h3>
                            <div class="aspect-video w-full max-w-2xl">
                                <iframe class="w-full h-full rounded shadow"
                                    src="https://www.youtube.com/embed/{{ str_replace('https://www.youtube.com/watch?v=', '', $c->entrevista->youtube_url) }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="bg-white p-6 rounded-lg shadow mb-10">
            <h2 class="text-2xl font-bold mb-4 text-center">📊 Resultados del Sondeo en Tiempo Real</h2>
            <div class="max-w-3xl mx-auto"><canvas id="resultsChart"></canvas></div>
        </div>

        <div class="text-center text-gray-500 text-sm pb-8">
            <p>Aplicación de estudio informativo · No tiene fines electorales oficiales</p>
            <p class="mt-2">Datos basados en entrevistas de Jordi Wild (Noviembre 2026)</p>
        </div>
    </div>

    <!-- ===== GRÁFICO ===== -->
    <script>
        const ctx = document.getElementById('resultsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($resultados->pluck('nombre')) !!},
                datasets: [{
                    label: 'Votos',
                    data: {!! json_encode($resultados->pluck('votos_count')) !!},
                    backgroundColor: {!! json_encode($resultados->pluck('color_hex')) !!},
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false } } }
        });
    </script>

    <!-- ===== PORTADA + MÚSICA + PWA ===== -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }

        let audioCtx, masterGain, playing = false;

        function iniciarMusica() {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            masterGain = audioCtx.createGain();
            masterGain.gain.value = 0.06;
            masterGain.connect(audioCtx.destination);

            const acordes = [ [220,277.18,329.63], [196,246.94,293.66], [174.61,220,261.63], [164.81,207.65,246.94] ];
            let i = 0;
            function tocarAcorde() {
                if (!playing) return;
                const t = audioCtx.currentTime;
                acordes[i % acordes.length].forEach(freq => {
                    const osc = audioCtx.createOscillator();
                    const g = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = freq;
                    g.gain.setValueAtTime(0, t);
                    g.gain.linearRampToValueAtTime(0.4, t + 1.5);
                    g.gain.linearRampToValueAtTime(0, t + 5);
                    osc.connect(g); g.connect(masterGain);
                    osc.start(t); osc.stop(t + 5);
                });
                i++;
                setTimeout(tocarAcorde, 4500);
            }
            tocarAcorde();
        }

        function entrar() {
            document.getElementById('portada').classList.add('fade-out');
            document.getElementById('btnMute').classList.remove('hidden');
            playing = true;
            iniciarMusica();
        }

        function toggleMute() {
            if (!audioCtx) return;
            playing = !playing;
            masterGain.gain.setTargetAtTime(playing ? 0.06 : 0, audioCtx.currentTime, 0.2);
            document.getElementById('btnMute').textContent = playing ? '🔊' : '🔇';
        }
    </script>
</body>
</html>
