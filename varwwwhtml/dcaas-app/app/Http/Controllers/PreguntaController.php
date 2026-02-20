<?php

namespace App\Http\Controllers;

use App\Enums\TipoPregunta;
use App\Http\Requests\EstablecerPreguntasRequest;
use App\Models\Pregunta;
use Illuminate\Http\Request;
use App\Enums\EstadoEncuesta;
use App\Facades\ManejadorPermisos;
use App\Models\Encuesta;
use Illuminate\Routing\Controller;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;

/**
 * Controller con las funciones relacionadas explicitamente con las preguntas de las encuestas
 */
class PreguntaController extends Controller
{

    public static $entregablesPublicos = ['id', 'titulo', 'descripcion', 'contenido', 'opcional', 'tipo', 'id_encuesta', 'placeholder', 'subtitulo']; //Campos considerados publicos a la hora de entregar
    public static $entregablesPrivados = ['id', 'titulo', 'descripcion', 'contenido', 'opcional', 'tipo', 'id_encuesta', 'placeholder', 'subtitulo', 'correcta']; //Campos considerados privados a la hora de entregar
    public static $tamagnoPagina = 50; //El tamagno por defecto de paginacion

    /**
     * Convierte los datos de una encuesta en JSON al formato que se usa en la base de datos
     * @param array $preguntas
     * @param string $id_encuesta
     * @return array
     */
    public static function formatearPreguntasADB(array $preguntas, string $id_encuesta): array
    {
        foreach ($preguntas as &$pregunta) {
            switch ($pregunta['tipo']) {
                case TipoPregunta::Desarrollar->value:
                    $pregunta['placeholder'] = (string) $pregunta['placeholder'] . '';
                    $pregunta['correcta'] = (string) $pregunta['correcta'] . '';
                    $pregunta['contenido'] = '';
                    break;
                case TipoPregunta::Check->value:
                    $pregunta['placeholder'] = is_array($pregunta['placeholder'] ?? null) ? implode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['placeholder']) : '';
                    $pregunta['correcta'] = is_array($pregunta['correcta'] ?? null) ? implode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['correcta']) : '';
                    asort($pregunta['contenido']);
                    $pregunta['contenido'] = implode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['contenido']);
                    break;
                case TipoPregunta::Radio->value:
                    $pregunta['placeholder'] = is_numeric($pregunta['placeholder'] ?? null) ? (string) $pregunta['placeholder'] : '';
                    $pregunta['correcta'] = is_numeric($pregunta['correcta'] ?? null) ? (string) $pregunta['correcta'] : '';
                    $pregunta['contenido'] = implode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['contenido']);
                    break;
                case TipoPregunta::Numero->value:
                    $pregunta['placeholder'] = (string) $pregunta['placeholder'] . '';
                    $pregunta['correcta'] = (string) $pregunta['correcta'] . '';
                    $pregunta['contenido'] = '';
                    break;
                default:
                    $pregunta = null;
            }
            $pregunta['id_encuesta'] = $id_encuesta;
        }
        return $preguntas;
    }

    /**
     * Formatea los datos de las preguntas guardadas en la base de datos a formato JSON
     * @param array $preguntas
     * @return array
     */
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

    /**
     * Establecer (reemplazando o no) las preguntas de una encuesta
     * @param EstablecerPreguntasRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
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
            $i = 0;
            $insercion = [];
            foreach ($datos['preguntas'] as $pregunta) {
                if (!is_null($pregunta)) {
                    $datosIteracion = $pregunta;
                    $datosIteracion['orden'] = $i;
                    $datosIteracion['id'] = (string) \Illuminate\Support\Str::uuid();
                    $datosIteracion['created_at'] = now();
                    $datosIteracion['updated_at'] = now();
                    if (!array_key_exists('contenido', $datosIteracion)) $datosIteracion['contenido'] = '';
                    if (!array_key_exists('descripcion', $datosIteracion)) $datosIteracion['descripcion'] = '';
                    if (!array_key_exists('subtitulo', $datosIteracion)) $datosIteracion['subtitulo'] = '';
                    if (!array_key_exists('placeholder', $datosIteracion)) $datosIteracion['placeholder'] = '';
                    if (!array_key_exists('correcta', $datosIteracion)) $datosIteracion['correcta'] = '';
                    $insercion[] = $datosIteracion;
                    //Pregunta::create($pregunta);
                }
                $i++;
            }
            if (empty($insercion)) return RespuestaAPI::fallo(422, 'Ha habido un error al insertar las preguntas, seguramente se deba a un error de formateo o un error interno.');
            Pregunta::insert($insercion);

            return RespuestaAPI::exito('Preguntas creadas con éxito', ['preguntas' => 'Usa la ruta de ver preguntas para mas detalles', 'destructivo' => $datos['destructivo']]);
            //Manera alternativa:
            //return RespuestaAPI::exito('Preguntas creadas con éxito', ['preguntas' => $datos['preguntas'], 'destructivo' => $datos['destructivo']]);
        } catch (\Exception $e) {
            dd($e);
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Ver las preguntas de cualquier encuesta publica
     * @param mixed $id
     * @param mixed $pagina
     * @return \Illuminate\Http\JsonResponse
     */
    public function verDeEncuesta($id, $pagina = 1)
    {
        try {
            $pagina = (int) $pagina ?? 1;
            if (is_null($pagina) || !is_int($pagina) || $pagina < 0)
                $pagina = 1;
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['publico'] == false)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $preguntas = Pregunta::where('id_encuesta', $id)->select(self::$entregablesPublicos)->orderBy('orden', 'asc')->skip(($pagina - 1) * self::$tamagnoPagina)->take(self::$tamagnoPagina)->get();
            $preguntas = self::formatearPreguntasDesdeDB($preguntas->toArray());
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Ver las preguntas de una encuesta de ese usuario aunque sea privada
     * @param Request $request
     * @param mixed $id
     * @param mixed $pagina
     * @return \Illuminate\Http\JsonResponse
     */
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
            $preguntas = Pregunta::where('id_encuesta', $id)->select(self::$entregablesPrivados)->orderBy('orden', 'asc')->skip(($pagina - 1) * self::$tamagnoPagina)->take(self::$tamagnoPagina)->get();
            $preguntas = self::formatearPreguntasDesdeDB($preguntas->toArray());
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }
}
