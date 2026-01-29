<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

//Este archivo es api.php, todas las rutas empezarán por /api

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//RUTAS PÚBLICAS

//Ruta de ejemplo, ante GET /api/test manda un json con esos datos y código 200
Route::get('/test', function (Request $request) {
    Log::info('Test', ['user' => "???"]); //Lanzar mensaje a la consola "tail -f storage/logs/laravel.log"
    return response()->json([
        'status' => 'success',
        'message' => 'Funciona correctamente',
        'timestamp' => now()
    ], 200);
});

//RUTAS PRIVADAS
//hacer antes los middlewares
