<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Responses\RespuestaAPI;
use App\Facades\ManejadorPermisos;

class Publicante
{
    /**
     * Indica que el usuario tiene el rol de publicante de encuestas
     * Osea que no esté deshabilitado, porque deshabilitado previene el login
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        if ($usuario && ManejadorPermisos::esPublicante($usuario)) {
            return $next($request);
        }
        return RespuestaAPI::fallo(403, 'Acceso denegado: Se requiere un usuario publicante (no un votante)');
    }
}
