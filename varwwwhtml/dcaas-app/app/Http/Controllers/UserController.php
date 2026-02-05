<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarUsuarioRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Responses\RespuestaAPI;

class UserController extends Controller
{

    /**
     *
     */
    public function registrar(RegistrarUsuarioRequest $request)
    {
        try {
            $user = User::create($request->validated());
            $token = $user->createToken('auth_token')->plainTextToken;

            return RespuestaAPI::exito('Usuario registrado con éxito', ["usuario" => $user, "token" => $token, 'token_type' => 'Bearer',]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(["info" => $e]);
        }

    }

    /**
     *
     */
    public function ver(User $user)
    {
        return RespuestaAPI::exito('ver');
    }

    /**
     *
     */
    public function login(User $user)
    {
        return RespuestaAPI::exito('login');
    }
}
