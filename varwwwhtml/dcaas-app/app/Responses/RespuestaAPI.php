<?php

namespace App\Responses;

/**
 * Mandar mas rápidamente respuestas JSON de la API
 */
class RespuestaAPI
{
    /**
     * Responde con una respuesta exitosa y ciertos datos
     * @param mixed $mensaje
     * @param mixed $datos
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Responde con una respuesta fallida con ciertos datos
     * @param mixed $codigo
     * @param mixed $mensaje
     * @param mixed $datos
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Responde con un fallo interno
     * @param mixed $datos
     * @param mixed $codigo
     * @param mixed $mensaje
     * @return \Illuminate\Http\JsonResponse
     */
    public static function falloInterno(?array $datos = null, ?int $codigo = 500, ?string $mensaje = '')
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
