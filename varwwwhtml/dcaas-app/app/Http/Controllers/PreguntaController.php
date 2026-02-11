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
            $previoPreguntas = $datos->preguntas;
            foreach ($datos['preguntas'] as $pregunta) {
                switch ($pregunta['tipo']) {
                    case TipoPregunta::Desarrollar:

                        break;
                    case TipoPregunta::Check:
                        //TODO: aaaa
                        break;
                    case TipoPregunta::Radio:
                        $pregunta['placeholder'] += "";
                        $pregunta['correcta'] += "";
                        break;
                    case TipoPregunta::Numero:
                        $pregunta['placeholder'] += "";
                        $pregunta['correcta'] += "";
                        break;
                    default:
                        return RespuestaAPI::fallo(422, 'Hay una pregunta mal formateada');
                }
                Pregunta::create($datos);
            }
            return RespuestaAPI::exito('Preguntas creadas con éxito', ['preguntas' => $previoPreguntas]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function verDeEncuesta($id)
    {
        try {
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['publico'] == false)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $preguntas = Pregunta::where('id_encuesta', $encuesta->id)->select(self::$entregablesPublicos)->get();
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas->only(self::$entregablesPublicos)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function verDeEncuestaPrivado(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $preguntas = Pregunta::where('id_encuesta', $encuesta->id)->select(self::$entregablesPublicos)->get();
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas->only(self::$entregablesPrivados)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }
}
