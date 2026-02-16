<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Responses\RespuestaAPI;
use App\Enums\PermisosUsuario;
use App\Facades\ManejadorPermisos;

class Admin
{
    /**
     * Indica que el usuario es administrador
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        if ($usuario && ManejadorPermisos::esAdmin($usuario)) {
            return $next($request);
        }
        return RespuestaAPI::fallo(403, 'Acceso denegado: Se requiere un usuario administrador');
    }
}
