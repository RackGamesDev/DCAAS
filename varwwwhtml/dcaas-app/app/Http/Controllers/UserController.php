<?php

namespace App\Http\Controllers;

use App\Enums\PermisosUsuario;
use App\Http\Requests\BorrarUsuarioRequest;
use App\Http\Requests\LoginUsuarioRequest;
use App\Http\Requests\RegistrarUsuarioRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\EditarUsuarioRequest;
use App\Facades\ManejadorPermisos;

class UserController extends Controller
{

    /**
     *
     */
    public function registrar(RegistrarUsuarioRequest $request)
    {
        try {
            $user = User::create($request->validated());
            //$user->permisos = 0;
            $token = $user->createToken('auth_token')->plainTextToken;

            return RespuestaAPI::exito('Usuario registrado con éxito', ['usuario' => $user->only(['nickname', 'nombre', 'descripcion', 'url_foto', 'id', 'permisos', 'fecha_creacion', 'email']), 'token' => $token, 'token_type' => 'Bearer',]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }

    }

    /**
     *
     */
    public function ver($id)
    {
        $user = User::find($id);
        if (!$user || ManejadorPermisos::todoRestringido($user))
            return RespuestaAPI::fallo(404, 'Usuario no encontrado');
        return RespuestaAPI::exito('Usuario encontrado correctamente', ['usuario' => $user->only(['nickname', 'nombre', 'descripcion', 'url_foto', 'id', 'permisos', 'fecha_creacion'])]);
    }

    public function verYo(Request $request)
    {
        $user = $request->user();
        //$token = $user->currentAccessToken();
        return RespuestaAPI::exito('Tus datos', [
            'usuario' => $user->only(['nickname', 'nombre', 'descripcion', 'url_foto', 'id', 'permisos', 'fecha_creacion', 'email']),
            //'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     *
     */
    public function login(LoginUsuarioRequest $request)
    {
        $datos = $request->validated();
        $user = User::where('nickname', $datos['auth'])
            ->orWhere('email', $datos['auth'])
            ->first();
        if (!$user || !Hash::check($datos['password'], $user->password)) {
            return RespuestaAPI::fallo(401, 'Credenciales incorrectas');
        }
        if (ManejadorPermisos::todoRestringido($user))
            return RespuestaAPI::fallo(401, 'No tienes permisos para realizar esta acción');
        $user->tokens()->delete();
        $token = $user->createToken('login_token')->plainTextToken;
        return RespuestaAPI::exito('Login exitoso', [
            'usuario' => $user->only(['nickname', 'nombre', 'descripcion', 'url_foto', 'id', 'permisos', 'fecha_creacion', 'email']),
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function editar(EditarUsuarioRequest $request)
    {
        $user = $request->user();
        $datos = $request->validated();
        if (!ManejadorPermisos::puedeAutoeditar($user))
            return RespuestaAPI::fallo(401, 'No tienes permisos para realizar esta acción');
        if (!empty($datos['permisos']) || !empty($datos['publicante']) || !empty($datos['fecha_creacion']) || !empty($datos['id']) || !empty($datos['token']))
            return RespuestaAPI::fallo(422, 'Hay parámetros que no tienes permitido editar');
        if (!empty($datos['nickname']) || !empty($datos['email']) || !empty($datos['password'])) {
            if (!Hash::check($request->oldPassword, $user->password)) {
                return RespuestaAPI::fallo(401, 'La contraseña antigua es incorrecta');
            }
        }
        if (isset($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        }
        $user->fill($datos);
        $user->save();
        return RespuestaAPI::exito('Usuario actualizado correctamente', [
            'usuario' => $user->only(['id', 'nickname', 'nombre', 'email', 'descripcion', 'url_foto', 'permisos', 'fecha_creacion'])
        ]);
    }

    public function borrar(BorrarUsuarioRequest $request)
    {
        $user = $request->user();
        $datos = $request->validated();
        if (!$user || !Hash::check($datos['password'], $user->password)) {
            return RespuestaAPI::fallo(401, 'Credenciales incorrectas');
        }
        $user->tokens()->delete();
        $user->delete();

        //TODO: borrado en cascada

        return RespuestaAPI::exito('Usuario borrado correctamente', [
            'user' => $user->only(['id', 'nickname', 'nombre', 'email', 'descripcion', 'url_foto', 'permisos', 'fecha_creacion'])
        ]);
    }

    public function cerrarSesion(Request $request)
    {
        $user = $request->user();
        if (!$user)
            return RespuestaAPI::fallo(401, 'Credenciales incorrectas');
        $user->tokens()->delete();
        return RespuestaAPI::exito('Sesión cerrada correctamente', [
            'usuario' => $user->only(['id', 'nickname', 'nombre', 'email', 'descripcion', 'url_foto', 'permisos', 'fecha_creacion'])
        ]);
    }
}
