<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observatorio Wild 2026</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f59e0b">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #portada { transition: transform .8s ease, opacity .8s ease; }
        .fade-out { transform: translateY(-100%); opacity: 0; pointer-events: none; }
        .hidden-portada { display: none !important; }
        .btn-votado { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
    </style>
</head>
<body class="bg-gray-50">

    <div id="portada" class="fixed inset-0 z-50 flex items-center justify-center"
         style="background: linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#312e81 100%);">
        <div class="text-center px-6">
            <p class="text-amber-400 tracking-widest text-sm font-semibold mb-4">THE WILD PROJECT · NOVIEMBRE 2026</p>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4">🗳️ Observatorio Wild</h1>
            <p class="text-slate-300 text-lg max-w-xl mx-auto mb-10">
                Sondeo informativo sobre las entrevistas a los candidatos a las Elecciones Generales 2026.
            </p>
            <button onclick="entrar()"
                class="px-10 py-4 bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold text-xl rounded-full shadow-2xl transition transform hover:scale-105">
                ▶ ENTRAR
            </button>
        </div>
    </div>

    <div id="bannerSonido" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-40 bg-amber-500 text-slate-900 px-6 py-3 rounded-full shadow-lg font-bold cursor-pointer hover:bg-amber-400 transition">
        🔊 Haz clic para activar el sonido
    </div>

    <button id="btnMute" onclick="toggleMute()" class="fixed bottom-4 right-4 z-40 bg-slate-800 text-white w-12 h-12 rounded-full shadow-lg hidden">🔊</button>

    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-2">🗳️ Sondeo Informativo: Elecciones 2026</h1>
        <p class="text-center text-gray-600 mb-4">Entrevistas de Jordi Wild</p>

        <div id="mensaje" class="hidden mb-4 px-4 py-3 rounded shadow"></div>

        <div id="avisoVotado" class="hidden mb-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 px-4 py-3 rounded shadow">
            ⚠️ Ya has emitido tu voto. Solo se permite un voto por dispositivo.
        </div>

        <div class="grid gap-6 mb-10">
            @foreach($candidatos as $c)
                <div class="bg-white p-6 rounded-lg shadow border-l-4" style="border-color: {{ $c->color_hex }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold">{{ $c->nombre }} <span class="text-sm font-normal text-gray-500">({{ $c->partido }})</span></h2>
                            <p class="text-gray-600 mt-2">{{ $c->resumen }}</p>
                        </div>
                        <button type="button" id="btn-votar-{{ $c->id }}" onclick="votar({{ $c->id }})" class="btn-votar px-6 py-3 text-white font-bold rounded hover:opacity-90 transition shadow-lg ml-4" style="background-color: {{ $c->color_hex }}">
                            🗳️ Votar
                        </button>
                    </div>

                    @if($c->propuestas->count() > 0)
                        <div class="mt-4 bg-gray-50 p-4 rounded">
                            <h3 class="font-semibold text-gray-700 mb-3">📋 Propuestas clave:</h3>
                            <div class="grid gap-2">
                                @foreach($c->propuestas as $p)
                                    <div class="bg-white p-3 rounded border-l-2" style="border-color: {{ $c->color_hex }}">
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-2">{{ $p->tema }}</span>
                                        <strong>{{ $p->titulo }}:</strong>
                                        <span class="text-gray-600 text-sm">{{ $p->descripcion }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($c->entrevista)
                        <div class="mt-4">
                            <h3 class="font-semibold text-gray-700 mb-2">🎥 Entrevista:</h3>
                            <div class="aspect-video w-full max-w-2xl">
                                <iframe class="w-full h-full rounded shadow"
                                    src="https://www.youtube.com/embed/{{ str_replace('https://www.youtube.com/watch?v=', '', $c->entrevista->youtube_url) }}"
                                    frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="bg-white p-6 rounded-lg shadow mb-10">
            <h2 class="text-2xl font-bold mb-4 text-center">📊 Resultados en Tiempo Real</h2>
            <div class="max-w-3xl mx-auto"><canvas id="resultsChart"></canvas></div>
            <p class="text-center text-xs text-gray-400 mt-2">🔄 Actualización automática cada 5 segundos</p>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('resultsChart').getContext('2d');
        let chart = new Chart(ctx, {
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

        // Polling: actualizar gráfico cada 5 segundos
        setInterval(function() {
            fetch('/resultados')
                .then(r => r.json())
                .then(d => {
                    chart.data.labels = d.labels;
                    chart.data.datasets[0].data = d.data;
                    chart.data.datasets[0].backgroundColor = d.colors;
                    chart.update();
                });
        }, 5000);

        let audioCtx, masterGain, playing = false;

        function iniciarMusica() {
            if (audioCtx) return;
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            masterGain = audioCtx.createGain();
            masterGain.gain.value = 0.06;
            masterGain.connect(audioCtx.destination);
            const acordes = [[220,277.18,329.63], [196,246.94,293.66], [174.61,220,261.63], [164.81,207.65,246.94]];
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
            sessionStorage.setItem('ya_entro', 'true');
            document.getElementById('portada').classList.add('fade-out');
            document.getElementById('btnMute').classList.remove('hidden');
            playing = true;
            iniciarMusica();
            comprobarSiYaVoto();
        }

        function activarSonido() {
            document.getElementById('bannerSonido').classList.add('hidden');
            document.getElementById('btnMute').classList.remove('hidden');
            playing = true;
            iniciarMusica();
            comprobarSiYaVoto();
        }

        if (sessionStorage.getItem('ya_entro') === 'true') {
            document.getElementById('portada').classList.add('hidden-portada');
            document.getElementById('bannerSonido').classList.remove('hidden');
            document.getElementById('bannerSonido').onclick = activarSonido;
            comprobarSiYaVoto();
        }

        function toggleMute() {
            if (!audioCtx) return;
            playing = !playing;
            masterGain.gain.setTargetAtTime(playing ? 0.06 : 0, audioCtx.currentTime, 0.2);
            document.getElementById('btnMute').textContent = playing ? '🔊' : '🔇';
        }

        // Comprobar si ya votó en este dispositivo (localStorage)
        function comprobarSiYaVoto() {
            if (localStorage.getItem('ya_vote') === 'true') {
                bloquearBotones();
            }
        }

        function bloquearBotones() {
            document.querySelectorAll('.btn-votar').forEach(btn => {
                btn.classList.add('btn-votado');
                btn.textContent = '✅ Votado';
                btn.disabled = true;
            });
            document.getElementById('avisoVotado').classList.remove('hidden');
        }

        function votar(candidatoId) {
            // Bloqueo local inmediato
            if (localStorage.getItem('ya_vote') === 'true') {
                mostrarMensaje('⚠️ Ya has emitido tu voto desde este dispositivo.', false);
                return;
            }

            const msgDiv = document.getElementById('mensaje');
            msgDiv.classList.add('hidden');

            fetch('/votar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ candidato_id: candidatoId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Marcar como votado en localStorage (persistente)
                    localStorage.setItem('ya_vote', 'true');
                    localStorage.setItem('voto_candidato', candidatoId);
                    bloquearBotones();
                }
                mostrarMensaje(data.message, data.success);
            })
            .catch(err => {
                mostrarMensaje('❌ Error de conexión', false);
            });
        }

        function mostrarMensaje(texto, esExito) {
            const msgDiv = document.getElementById('mensaje');
            msgDiv.classList.remove('hidden');
            msgDiv.textContent = texto;
            msgDiv.className = 'mb-4 px-4 py-3 rounded shadow ' +
                (esExito ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 'bg-red-100 border-l-4 border-red-500 text-red-700');
        }
    </script>
</body>
</html>
