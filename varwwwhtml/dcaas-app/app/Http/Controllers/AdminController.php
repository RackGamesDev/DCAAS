<?php

namespace App\Http\Controllers;

use App\Enums\EstadoEncuesta;
use App\Facades\ManejadorPermisos;
use App\Http\Requests\AdminEditarUsuarioRequest;
use App\Http\Requests\CambiarPermisosRequest;
use App\Http\Requests\EditarEncuestaRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pregunta;
use App\Models\Encuesta;
use Illuminate\Routing\Controller;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController;

/**
 * Controller para las acciones relacionadas con los administradores, todas las funciones requieren de un token de sesion de un usaurio administrador
 */
class AdminController extends Controller
{

    /**
     * Edita los permisos de otro usuario
     * @param CambiarPermisosRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editarPermisos(CambiarPermisosRequest $request)
    {
        try {
            $usuarioPeticion = $request->user();
            if (!$usuarioPeticion || !ManejadorPermisos::esAdmin($usuarioPeticion)) return RespuestaAPI::fallo(403, 'No tienes permisos para esto');
            $datos = $request->validated();
            $user = User::where('id', $datos['id'])
                ->first();
            if (!$user)
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $edicion = ['permisos' => $datos['permisos']];
            $user->fill($edicion);
            $user->tokens()->delete();
            $user->save();
            return RespuestaAPI::exito('Permisos del usuario actualizados correctamente');
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }

    }

    /**
     * Edita los datos de otro usuario
     * @param AdminEditarUsuarioRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editarUsuarioAjeno(AdminEditarUsuarioRequest $request)
    {
        try {
            $usuarioPeticion = $request->user();
            if (!$usuarioPeticion || !ManejadorPermisos::esAdmin($usuarioPeticion)) return RespuestaAPI::fallo(403, 'No tienes permisos para esto');

            $user = $request->user();
            $datos = $request->validated();

            if (isset($datos['password'])) {
                $datos['password'] = Hash::make($datos['password']);
            }
            $user->fill($datos);
            $user->save();
            return RespuestaAPI::exito('Usuario actualizado correctamente', [
                'usuario' => $user->only(UserController::$entregablesPrivados)
            ]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }

    }

    /**
     * Borrar otro usuario (seria similar a banearlo), esto provocaria un borrado en cascada
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrarUsuarioAjeno(Request $request, $id)
    {
        try {
            $usuarioPeticion = $request->user();
            if (!$usuarioPeticion || !ManejadorPermisos::esAdmin($usuarioPeticion)) return RespuestaAPI::fallo(403, 'No tienes permisos para esto');

            $usuarioPeticion = $request->user();
            if (!$usuarioPeticion || !ManejadorPermisos::esAdmin($usuarioPeticion)) return RespuestaAPI::fallo(403, 'No tienes permisos para esto');

            $user = User::find($id);
            if (!$user)
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $user->tokens()->delete();
            $user->delete();

            //TODO: borrado en cascada

            return RespuestaAPI::exito('Usuario borrado correctamente');
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }

    }

    /**
     * Ver todos los datos de otro usuario
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verUsuarioAjeno(Request $request, $id)
    {
        try {
            $usuarioPeticion = $request->user();
            if (!$usuarioPeticion || !ManejadorPermisos::esAdmin($usuarioPeticion)) return RespuestaAPI::fallo(403, 'No tienes permisos para esto');

            $user = User::find($id);
            if (!$user)
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            return RespuestaAPI::exito('Usuario encontrado', ['usuario' => $user]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Edita los datos de cualquier encuesta que no haya iniciado aun
     * @param EditarEncuestaRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editarEncuestaAjena(EditarEncuestaRequest $request, $id) {
        try {
            $usuarioPeticion = $request->user();
            if (!$usuarioPeticion || !ManejadorPermisos::esAdmin($usuarioPeticion)) return RespuestaAPI::fallo(403, 'No tienes permisos para esto');
            $encuesta = Encuesta::find($id);
            if (!$encuesta) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta['estado'] != EstadoEncuesta::SinIniciar) return RespuestaAPI::fallo(403, 'A pesar de ser administrador, no puedes editar encuestas que hayan empezado');
            $datos = $request->validated();
            $encuesta->fill($datos);
            $encuesta->save();
            return RespuestaAPI::exito('Encuesta editada', ['encuesta' => $encuesta]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Borra una encuesta (que no deberia haber empezado) provocando un borrado en cascada
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrarEncuestaAjena(Request $request, $id) {
        try {
            $usuarioPeticion = $request->user();
            if (!$usuarioPeticion || !ManejadorPermisos::esAdmin($usuarioPeticion)) return RespuestaAPI::fallo(403, 'No tienes permisos para esto');
            $encuesta = Encuesta::find($id);
            if (!$encuesta) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta['estado'] == EstadoEncuesta::Activa) return RespuestaAPI::fallo(403, 'A pesar de ser administrador, no puedes borrar una encuesta que esté activa');
            $encuesta->delete();
            return RespuestaAPI::exito('Encuesta borrada', ['encuesta' => $encuesta]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Ver todos los datos de cualquier encuesta
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verEncuestaAjena(Request $request, $id) {
        try {
            $usuarioPeticion = $request->user();
            if (!$usuarioPeticion || !ManejadorPermisos::esAdmin($usuarioPeticion)) return RespuestaAPI::fallo(403, 'No tienes permisos para esto');
            $encuesta = Encuesta::find($id);
            if (!$encuesta) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            return RespuestaAPI::exito('Encuesta encontrada', ['encuesta' => $encuesta]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Ver las preguntas de cualquier encuesta
     * @param Request $request
     * @param mixed $id
     * @param mixed $pagina
     * @return \Illuminate\Http\JsonResponse
     */
    public function verPreguntasEncuestaAjena(Request $request, $id, $pagina = 1)
    {
        try {
            $pagina = (int) $pagina ?? 1;
            if (is_null($pagina) || !is_int($pagina) || $pagina < 0)
                $pagina = 1;
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esAdmin($user))
                return RespuestaAPI::fallo(404, 'No tienes permisos');
            $encuesta = Encuesta::find($id);
            if (!$encuesta)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $preguntas = Pregunta::where('id_encuesta', $id)->select(PreguntaController::$entregablesPrivados)->skip(($pagina - 1) * PreguntaController::$tamagnoPagina)->take(PreguntaController::$tamagnoPagina)->get();
            $preguntas = PreguntaController::formatearPreguntasDesdeDB($preguntas->toArray());
            return RespuestaAPI::exito('Preguntas de esa encuesta', ['preguntas' => $preguntas]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

}
