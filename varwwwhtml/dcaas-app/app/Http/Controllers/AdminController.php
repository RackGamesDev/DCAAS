<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminEditarUsuarioRequest;
use App\Http\Requests\CambiarPermisosRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController;


class AdminController extends Controller
{
    public function editarPermisos(CambiarPermisosRequest $request)
    {
        try {
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

    public function editarUsuarioAjeno(AdminEditarUsuarioRequest $request)
    {
        try {
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

    public function borrarUsuarioAjeno($id)
    {
        try {
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

    public function verUsuarioAjeno($id)
    {
        try {
            $user = User::find($id);
            if (!$user)
                return RespuestaAPI::fallo(404, 'Usuario no encontrado');
            return RespuestaAPI::exito('Usuario encontrado', ['usuario' => $user]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }

    }



    //TODO: funciones sobre encuestas



}
