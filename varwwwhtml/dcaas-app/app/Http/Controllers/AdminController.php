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


class AdminController extends Controller
{
    public function editarPermisos(CambiarPermisosRequest $request)
    {
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
    }

    public function editarUsuarioAjeno(AdminEditarUsuarioRequest $request)
    {
        $user = $request->user();
        $datos = $request->validated();

        if (isset($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        }
        $user->fill($datos);
        $user->save();
        return RespuestaAPI::exito('Usuario actualizado correctamente', [
            'usuario' => $user->only(['id', 'nickname', 'nombre', 'email', 'descripcion', 'url_foto', 'permisos', 'fecha_creacion'])
        ]);
    }

    public function borrarUsuarioAjeno($id)
    {
        $user = User::find($id);
        if (!$user)
            return RespuestaAPI::fallo(404, 'Usuario no encontrado');
        $user->tokens()->delete();
        $user->delete();

        //TODO: borrado en cascada

        return RespuestaAPI::exito('Usuario borrado correctamente');
    }

    public function verUsuarioAjeno($id)
    {
        $user = User::find($id);
        if (!$user)
            return RespuestaAPI::fallo(404, 'Usuario no encontrado');
        return RespuestaAPI::exito('Usuario encontrado', ['usuario' => $user]);
    }
}
