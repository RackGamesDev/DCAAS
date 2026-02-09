<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Responses\RespuestaAPI;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        //apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'logeable'   => \App\Http\Middleware\Logeable::class,
            'admin'      => \App\Http\Middleware\Admin::class,
            'editor'     => \App\Http\Middleware\Editor::class,
            'publicante' => \App\Http\Middleware\Publicante::class,
            'votante'    => \App\Http\Middleware\Votante::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return RespuestaAPI::fallo(405, 'Método no permitido.');
            }
        });
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return RespuestaAPI::fallo(404, 'Ruta no encontrada.');
            }
        });
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return RespuestaAPI::fallo(401, 'Token de sesión inválido o ausente');
            }
        });
    })->create();
