<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidato;
use App\Models\Entrevista;
use App\Models\Propuesta;

class DatosElectoralesSeeder extends Seeder
{
    public function run(): void
    {
        $datos = [
            ['nombre' => 'Pedro Sánchez', 'partido' => 'PSOE', 'color' => '#eb1c24', 'youtube_id' => 'mStOFBvToUI',
             'resumen' => 'Defiende la continuidad del modelo de pactos, subida de pensiones vinculada al IPC, refuerzo de la sanidad pública y transición ecológica con creación de empleo.',
             'propuestas' => [
                ['tema' => 'Vivienda', 'titulo' => 'Parque público y alquiler joven', 'descripcion' => 'Ampliación del parque de vivienda pública y ayudas al alquiler para jóvenes.'],
                ['tema' => 'Sanidad', 'titulo' => 'Cobertura dental y salud mental', 'descripcion' => 'Cobertura dental completa y refuerzo de la salud mental en la sanidad pública.'],
                ['tema' => 'Economía', 'titulo' => 'Pensiones con IPC y SMI', 'descripcion' => 'Revalorización de pensiones según IPC real y subida del salario mínimo.'],
                ['tema' => 'Empleo', 'titulo' => 'Empleo verde', 'descripcion' => 'Creación de empleo ligado a la transición ecológica y energías renovables.'],
                ['tema' => 'Inmigración', 'titulo' => 'Integración ordenada', 'descripcion' => 'Vías legales de inmigración y políticas de integración y convivencia.'],
             ]],
            ['nombre' => 'Alberto Núñez Feijóo', 'partido' => 'PP', 'color' => '#1d4ed8', 'youtube_id' => '1YlGArMx_N4',
             'resumen' => 'Apuesta por la alternancia, bajada de impuestos, reforma fiscal para simplificar el sistema y recentralización de competencias para garantizar la igualdad en toda España.',
             'propuestas' => [
                ['tema' => 'Vivienda', 'titulo' => 'Un millón de viviendas', 'descripcion' => 'Construir un millón de viviendas, derogar la Ley de Vivienda, IVA del 4% y licencias en tres meses.'],
                ['tema' => 'Sanidad', 'titulo' => 'Reforzar hospitales públicos', 'descripcion' => 'Aumentar la plantilla del hospital público e impulsar la construcción de nuevos centros.'],
                ['tema' => 'Economía', 'titulo' => 'Rebaja fiscal general', 'descripcion' => 'Bajada general de impuestos y eliminación de tributos propios autonómicos.'],
                ['tema' => 'Empleo', 'titulo' => 'Simplificación laboral', 'descripcion' => 'Reducir cargas administrativas y facilitar la contratación en empresas.'],
                ['tema' => 'Inmigración', 'titulo' => 'Endurecimiento migratorio', 'descripcion' => 'Política migratoria más estricta y control reforzado de fronteras.'],
             ]],
            ['nombre' => 'Santiago Abascal', 'partido' => 'Vox', 'color' => '#16a34a', 'youtube_id' => 'pn8D-0KwecU',
             'resumen' => 'Línea dura en seguridad y fronteras, derogación de leyes de identidad de género, defensa de la unidad de España y bajada fiscal agresiva para empresas y autónomos.',
             'propuestas' => [
                ['tema' => 'Vivienda', 'titulo' => 'Residencia legal para alquilar', 'descripcion' => 'Exigir residencia legal para alquilar vivienda y desregular el mercado inmobiliario.'],
                ['tema' => 'Sanidad', 'titulo' => 'Gestión eficiente del SNS', 'descripcion' => 'Prioridad nacional en la sanidad y auditoría del gasto público sanitario.'],
                ['tema' => 'Economía', 'titulo' => 'Reforma fiscal integral', 'descripcion' => 'Simplificación y bajada del IRPF, derogar la reforma laboral y recuperar el plan nuclear.'],
                ['tema' => 'Empleo', 'titulo' => 'Desregulación laboral', 'descripcion' => 'Eliminar normas que frenan la competitividad y la libertad empresarial.'],
                ['tema' => 'Inmigración', 'titulo' => 'Remigración', 'descripcion' => 'Suprimir ayudas a inmigrantes ilegales, gravar remesas y limitar el IMV.'],
             ]],
            ['nombre' => 'Ada Colau', 'partido' => 'Frente Amplio', 'color' => '#a855f7', 'youtube_id' => '-ESmAhdixfo',
             'resumen' => 'Frente amplio de izquierdas, regulación estricta de alquileres, subida del SMI por encima del IPC, reducción de la jornada laboral y políticas feministas y ecologistas.',
             'propuestas' => [
                ['tema' => 'Vivienda', 'titulo' => 'Banco público de vivienda', 'descripcion' => 'Creación de un banco público de vivienda y regulación de precios de alquiler.'],
                ['tema' => 'Sanidad', 'titulo' => 'Ampliar cobertura pública', 'descripcion' => 'Incorporar dentistas, audífonos, salud visual y menopausia a la sanidad pública.'],
                ['tema' => 'Economía', 'titulo' => 'SMI al 60% del salario medio', 'descripcion' => 'Salario mínimo de al menos el 60% del salario medio y más intervención pública.'],
                ['tema' => 'Empleo', 'titulo' => 'Jornada de 37,5 horas', 'descripcion' => 'Reducción de jornada a 37,5 horas sin bajar sueldos y registro horario obligatorio.'],
                ['tema' => 'Inmigración', 'titulo' => 'Derechos y regularización', 'descripcion' => 'Defensa de los derechos de los inmigrantes y vías de regularización.'],
             ]],
        ];

        foreach ($datos as $d) {
            $c = Candidato::updateOrCreate(
                ['nombre' => $d['nombre']],
                ['partido' => $d['partido'], 'color_hex' => $d['color'], 'resumen' => $d['resumen']]
            );

            Entrevista::updateOrCreate(
                ['candidato_id' => $c->id],
                ['titulo' => "Entrevista a {$d['nombre']} - The Wild Project", 'youtube_url' => "https://www.youtube.com/watch?v={$d['youtube_id']}", 'fecha' => now()->format('Y-m-d')]
            );

            $titulos = array_column($d['propuestas'], 'titulo');
            Propuesta::where('candidato_id', $c->id)->whereNotIn('titulo', $titulos)->delete();
            foreach ($d['propuestas'] as $p) {
                Propuesta::updateOrCreate(
                    ['candidato_id' => $c->id, 'titulo' => $p['titulo']],
                    ['tema' => $p['tema'], 'descripcion' => $p['descripcion']]
                );
            }
        }
    }
}
