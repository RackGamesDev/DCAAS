<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotarRequest;
use App\Models\Respuesta;
use Illuminate\Http\Request;
use App\Enums\EstadoEncuesta;
use Illuminate\Routing\Controller;
use App\Enums\PermisosUsuario;
use App\Models\User;
use App\Models\Encuesta;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use App\Facades\ManejadorPermisos;

/**
 * Controller para las funciones relacionadas con las respuestas, dependiente de usuarios y preguntas
 */
class RespuestaController extends Controller
{

    public static $tamagnoPagina = 50; //El tamagno por defecto de paginacion

    public static function respuestaADB(string $respuesta): string {

    }
    public static function DBARespuesta(string $respuesta): string {

    }

    /**
     * Un usuario vota en una encuesta, se espera recibir los datos de todas las respuestas para guardarlos de golpe
     * @param VotarRequest $request
     * @param mixed $id
     * @return void
     */
    public function votar(VotarRequest $request, $id) {
        try{
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esVotante($user) || !ManejadorPermisos::puedeEditar($user)) return RespuestaAPI::fallo(401, 'No tienes permisos para realizar esta acción');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['publico'] == false || $encuesta ['estado'] != EstadoEncuesta::Activa) return RespuestaAPI::fallo(404, 'La encuesta no ha empezado, no es pública o no existe');
            $yaRespondido = Respuesta::where('id_user', $user->id)->exists();
            if ($yaRespondido) return RespuestaAPI::fallo(401, 'No puedes responder dos veces');
            $datos = $request->datos;
            if ($encuesta['anonimo'] == true) {

            } else {

            }
            return RespuestaAPI::exito('Respuesta guardada correctamente');
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Si es el creador de la encuesta y esta no es anonima, ver las respuestas (si es anonima tambien se puede pero no se ve quien ha respondido)
     * @param Request $request
     * @param mixed $id
     * @param mixed $pagina
     * @return void
     */
    public function verRespuestasDeEncuesta(Request $request, $id, $pagina) {

    }

    /**
     * Sabiendo el UUID de una respuesta concreta, ver sus datos solo si el usuario es quien responde o es el creador de la encuesta y esta no es anonima (si lo es entonces no se ve el usuario que responde)
     * @param Request $request
     * @param mixed $id
     * @return void
     */
    public function verRespuestaDeEncuesta(Request $request, $id) {

    }



}
