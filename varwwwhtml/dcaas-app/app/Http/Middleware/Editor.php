<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use App\Facades\ManejadorPermisos;

class Editor
{
    /**
     * Indica que el usuario tiene permisos para interactuar con la app
     * Quiere decir que no está bloqueado (ni deshabilitado), porque bloqueado quita permisos de edición sobre lo que sea en la app
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        if ($usuario && ManejadorPermisos::puedeEditar($usuario)) {
            return $next($request);
        }
        return RespuestaAPI::fallo(403, 'Acceso denegado: Se requiere un usuario activado');
    }
}
