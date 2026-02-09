<?php

namespace App\Http\Controllers;

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

    public static $entregablesPublicos = ['id', 'titulo', 'descripcion', 'contenido', 'opcional', 'tipo', 'id_encuesta'];
    public static $entregablesPrivados = ['id', 'titulo', 'descripcion', 'contenido', 'opcional', 'tipo', 'id_encuesta'];

    public function establecer(EstablecerPreguntasRequest $request, $id) {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $datos = $request->validated();
            //TODO: reglas de validacion, borrar preguntas previas y poner las nuevas, y luego testear y rutas de admin
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function verDeEncuesta($id) {
        try {
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['publico'] == false) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $preguntas = Pregunta::where('id_encuesta', $encuesta->id)->select(self::$entregablesPublicos)->get();
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas->only(self::$entregablesPublicos)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function verDeEncuestaPrivado(Request $request, $id) {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $preguntas = Pregunta::where('id_encuesta', $encuesta->id)->select(self::$entregablesPublicos)->get();
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas->only(self::$entregablesPrivados)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }
}
