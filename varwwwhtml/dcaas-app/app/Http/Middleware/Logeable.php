<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Responses\RespuestaAPI;
use App\Enums\PermisosUsuario;
use App\Facades\ManejadorPermisos;

//Indica que el usuario es capaz de iniciar sesión
class Logeable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        if ($usuario && !ManejadorPermisos::todoRestringido($usuario)) {
            return $next($request);
        }
        return RespuestaAPI::fallo(403, 'Acceso denegado: Se requiere un usuario activado');
    }
}
