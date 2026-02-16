<?php

namespace App\Http\Controllers;

use App\Enums\TipoPregunta;
use App\Http\Requests\EstablecerPreguntasRequest;
use App\Models\Pregunta;
use Illuminate\Http\Request;
use App\Enums\EstadoEncuesta;
use App\Facades\ManejadorPermisos;
use App\Models\User;
use App\Models\Encuesta;
use Illuminate\Routing\Controller;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController;

class PreguntaController extends Controller
{

    public static $entregablesPublicos = ['id', 'titulo', 'descripcion', 'contenido', 'opcional', 'tipo', 'id_encuesta', 'placeholder', 'subtitulo'];
    public static $entregablesPrivados = ['id', 'titulo', 'descripcion', 'contenido', 'opcional', 'tipo', 'id_encuesta', 'placeholder', 'subtitulo', 'correcta'];
    public static $tamagnoPagina = 50;

    public static function formatearPreguntasADB(array $preguntas, string $id_encuesta): array
    {
        foreach ($preguntas as &$pregunta) {
            switch ($pregunta['tipo']) {
                case TipoPregunta::Desarrollar->value:
                    $pregunta['placeholder'] = (string) $pregunta['placeholder'] . "";
                    $pregunta['correcta'] = (string) $pregunta['correcta'] . "";
                    $pregunta['contenido'] = "";
                    break;
                case TipoPregunta::Check->value:
                    $pregunta['placeholder'] = is_array($pregunta['placeholder'] ?? null) ? implode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['placeholder']) : "";
                    $pregunta['correcta'] = is_array($pregunta['correcta'] ?? null) ? implode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['correcta']) : "";
                    $pregunta['contenido'] = implode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['contenido']);
                    break;
                case TipoPregunta::Radio->value:
                    $pregunta['placeholder'] = is_numeric($pregunta['placeholder'] ?? null) ? (string) $pregunta['placeholder'] : "";
                    $pregunta['correcta'] = is_numeric($pregunta['correcta'] ?? null) ? (string) $pregunta['correcta'] : "";
                    $pregunta['contenido'] = implode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['contenido']);
                    break;
                case TipoPregunta::Numero->value:
                    $pregunta['placeholder'] = (string) $pregunta['placeholder'] . "";
                    $pregunta['correcta'] = (string) $pregunta['correcta'] . "";
                    $pregunta['contenido'] = "";
                    break;
                default:
                    $pregunta = null;
            }
            $pregunta['id_encuesta'] = $id_encuesta;
        }
        return $preguntas;
    }

    public static function formatearPreguntasDesdeDB(array $preguntas): array
    {
        foreach ($preguntas as &$pregunta)
            switch ($pregunta['tipo']) {
                case TipoPregunta::Desarrollar->value:
                    $pregunta['contenido'] = null;
                    break;
                case TipoPregunta::Check->value:
                    $pregunta['contenido'] = explode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['contenido']) ?? [];
                    $pregunta['placeholder'] = is_array($pregunta['placeholder'] ?? null) ? (explode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['placeholder']) ?? []) : null;
                    $pregunta['correcta'] = is_array($pregunta['correcta'] ?? null) ? (explode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['correcta']) ?? []) : null;
                    break;
                case TipoPregunta::Radio->value:
                    $pregunta['correcta'] = is_numeric($pregunta['correcta'] ?? null) ? (int) $pregunta['correcta'] : null;
                    $pregunta['placeholder'] = is_numeric($pregunta['placeholder'] ?? null) ? (int) $pregunta['placeholder'] : null;
                    $pregunta['contenido'] = explode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['contenido']) ?? [];
                    break;
                case TipoPregunta::Numero->value:
                    $pregunta['placeholder'] = is_numeric($pregunta['placeholder'] ?? null) ? (float) $pregunta['placeholder'] : null;
                    $pregunta['correcta'] = is_numeric($pregunta['correcta'] ?? null) ? (float) $pregunta['correcta'] : null;
                    $pregunta['contenido'] = null;
                    break;
                default:
                    $pregunta = null;
            }
        return $preguntas;
    }

    public function establecer(EstablecerPreguntasRequest $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta->estado != EstadoEncuesta::SinIniciar)
                return RespuestaAPI::fallo(403, 'No puedes cambiar las preguntas de una encuesta que ya haya empezado');
            $datos = $request->validated();
            if ($datos['destructivo'] == true) {
                Pregunta::where('id_encuesta', $encuesta->id)->delete(); //Borrar previas.
            } else {
                if (Pregunta::where('id_encuesta', $encuesta->id)->whereIn('titulo', collect($datos['preguntas'])->pluck('titulo'))->exists())
                    return RespuestaAPI::fallo(422, 'Uno o más títulos de preguntas ya existen en esta encuesta.');
            }
            $previoPreguntas = $datos['preguntas'];
            $datos['preguntas'] = self::formatearPreguntasADB($previoPreguntas, $encuesta->id);
            foreach ($datos['preguntas'] as $pregunta)
                if (!is_null($pregunta))
                    Pregunta::create($pregunta);
            return RespuestaAPI::exito('Preguntas creadas con éxito', ['preguntas' => $previoPreguntas, 'destructivo' => $datos['destructivo']]);
            //Manera alternativa:
            //return RespuestaAPI::exito('Preguntas creadas con éxito', ['preguntas' => $datos['preguntas'], 'destructivo' => $datos['destructivo']]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function verDeEncuesta($id, $pagina = 1)
    {
        try {
            $pagina = (int) $pagina ?? 1;
            if (is_null($pagina) || !is_int($pagina) || $pagina < 0)
                $pagina = 1;
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['publico'] == false)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $preguntas = Pregunta::where('id_encuesta', $id)->select(self::$entregablesPublicos)->skip(($pagina - 1) * self::$tamagnoPagina)->take(self::$tamagnoPagina)->get();
            $preguntas = self::formatearPreguntasDesdeDB($preguntas->toArray());
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function verDeEncuestaPrivado(Request $request, $id, $pagina = 1)
    {
        try {
            $pagina = (int) $pagina ?? 1;
            if (is_null($pagina) || !is_int($pagina) || $pagina < 0)
                $pagina = 1;
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $preguntas = Pregunta::where('id_encuesta', $id)->select(self::$entregablesPrivados)->skip(($pagina - 1) * self::$tamagnoPagina)->take(self::$tamagnoPagina)->get();
            $preguntas = self::formatearPreguntasDesdeDB($preguntas->toArray());
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }
}
