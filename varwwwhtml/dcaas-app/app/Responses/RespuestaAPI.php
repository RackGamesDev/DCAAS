<?php

namespace App\Responses;

//Mandar mas rápidamente respuestas JSON de la API
class RespuestaAPI
{
    public static function exito(?string $mensaje = '', ?array $datos = null)
    {
        return response()->json([
            'status' => 'success',
            'message' => $mensaje ?? '',
            'data' => $datos ?? null,
            'timestamp' => now(),
            'ok' => true,
            'code' => 200
        ], 200);
    }

    public static function fallo(?int $codigo = 400, ?string $mensaje = '', ?array $datos = null)
    {
        return response()->json([
            'status' => 'failure',
            'timestamp' => now(),
            'message' => $mensaje ?? 'Error',
            'ok' => false,
            'data' => $datos ?? null,
            'code' => $codigo ?? 400
        ], $codigo ?? 400);
    }

    public static function falloInterno(?int $codigo = 500, ?string $mensaje = '', ?array $datos = null)
    {
        return response()->json([
            'status' => 'internal failure',
            'timestamp' => now(),
            'message' => $mensaje ?? 'Error',
            'ok' => false,
            'data' => $datos ?? null,
            'code' => $codigo ?? 500
        ], $codigo ?? 500);
    }
}
