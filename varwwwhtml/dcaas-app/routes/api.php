<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Responses\RespuestaAPI;
use App\Http\Controllers\UserController;

//Este archivo es api.php, todas las rutas empezarán por /api

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//RUTAS PÚBLICAS

//Ruta de ejemplo, ante GET /api/test manda un json con esos datos y código 200
Route::get('/test', function (Request $request) {
    Log::info('Test', ['user' => "???"]); //Lanzar mensaje a la consola "tail -f storage/logs/laravel.log"
    return RespuestaAPI::exito('Funciona correctamente');
});

Route::get('/debug', function (Request $request) {
    if (config("app.debug")) { //Solo disponible cuando no está en producción
        Log::info('aaaa', ['obj' => "???"]);
        return RespuestaAPI::exito('aaa', null);
    }
    abort(404); //Si no, 404
});



//EDITAR
Route::get('/usuarios', [UserController::class, 'ver']);

//RUTAS PRIVADAS
//hacer antes los middlewares






//Ruta de error 404
Route::get('/{x}', function (Request $request) {
    return RespuestaAPI::fallo(404, "Error 404 route not found");
});






