<?php

namespace App\Http\Controllers;

use App\Enums\EstadoEncuesta;
use App\Http\Requests\CrearEncuestaRequest;
use App\Http\Requests\EditarEncuestaRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Enums\PermisosUsuario;
use App\Models\User;
use App\Models\Encuesta;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use App\Facades\ManejadorPermisos;


class EncuestaController extends Controller
{

    public static $entregablesPublicos = ['nombre', 'descripcion', 'url_foto', 'id', 'fecha_creacion', 'certificacion', 'votacion', 'anonimo', 'fecha_inicio', 'fecha_fin', 'estado', 'id_user'];
    public static $entregablesPrivados = ['nombre', 'descripcion', 'url_foto', 'id', 'fecha_creacion', 'certificacion', 'publico', 'votacion', 'anonimo', 'fecha_inicio', 'fecha_fin', 'estado', 'id_user'];
    public static $tamagnoPagina = 50;

    public function crear(CrearEncuestaRequest $request) {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user)) return RespuestaAPI::fallo(401, 'No tienes permisos para realizar esta acción');
            $datos = $request->validated();
            $datos['id_user'] = $user->id;
            $encuesta = Encuesta::create($datos);
            $encuesta->refresh();
            return RespuestaAPI::exito('Encuesta creada con éxito', ['encuesta' => $encuesta->only(self::$entregablesPrivados)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function verPublico($id) {
        try {
            $encuesta = Encuesta::find($id);
            if (!$encuesta || !$encuesta['publico']) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            return RespuestaAPI::exito('Encuesta encontrada', ['encuesta' => $encuesta->only(self::$entregablesPublicos)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function verPrivado(Request $request, $id) {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            return RespuestaAPI::exito('Encuesta encontrada', ['encuesta' => $encuesta->only(self::$entregablesPrivados)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function borrar(Request $request, $id) {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            $encuesta->delete();

            //TODO: borrado en cascada

            return RespuestaAPI::exito('Encuesta eliminada', ['encuesta' => $encuesta->only(self::$entregablesPrivados)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function editar(EditarEncuestaRequest $request, $id) {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta['estado'] != EstadoEncuesta::SinIniciar) return RespuestaAPI::fallo(406, 'Solo se puede editar una encuesta que no haya empezado aún');
            $datos = $request->validated();

            $encuesta->fill($datos);
            $encuesta->save();
            return RespuestaAPI::exito('Encuesta editada', ['encuesta' => $encuesta->only(self::$entregablesPrivados)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function iniciar(Request $request, $id) {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta['estado'] != EstadoEncuesta::SinIniciar) return RespuestaAPI::fallo(406, 'Solo se pueden iniciar encuestas sin iniciar');
            //dd($encuesta);
            if ($encuesta['publico'] == false) return RespuestaAPI::fallo(406, 'La encuesta debe de ser pública antes de iniciarla (al menos de momento)'); //TODO: iniciar privadas
            $encuesta->fill(['estado' => EstadoEncuesta::Activa, 'fecha_inicio' => now()]);
            $encuesta->save();
            return RespuestaAPI::exito('Encuesta iniciada', ['encuesta' => $encuesta->only(self::$entregablesPrivados)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function finalizar(Request $request, $id) {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user))
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id) return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta['estado'] != EstadoEncuesta::Activa) return RespuestaAPI::fallo(406, 'Solo se pueden finalizar encuestas que estén activas');
            $encuesta->fill(['estado' => EstadoEncuesta::Terminada, 'fecha_fin' => now()]);
            $encuesta->save();
            return RespuestaAPI::exito('Encuesta finalizada', ['encuesta' => $encuesta->only(self::$entregablesPrivados)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    public function buscar($busqueda, $pagina) {
        try {
            $pagina = (int)$pagina;
            if (is_null($pagina) || !is_int($pagina) || $pagina < 0) $pagina = 1;
            $encuestas = Encuesta::where('publico', true)->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($busqueda) . '%'])->select(self::$entregablesPublicos)->skip(($pagina - 1) * self::$tamagnoPagina)->take(self::$tamagnoPagina)->get();
            if (!$encuestas || $encuestas->isEmpty()) return RespuestaAPI::fallo(404, 'Encuestas no encontradas (que coincidan con la busqueda)');
            return RespuestaAPI::exito('Encuestas encontradas', ['encuestas' => $encuestas]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }
}
