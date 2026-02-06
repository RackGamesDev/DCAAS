<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Responses\RespuestaAPI;
use App\Http\Controllers\UserController;

//Este archivo es api.php, todas las rutas empezarán por /api

//RUTAS PÚBLICAS:

//Ruta de ejemplo, ante GET /api/test manda un json con esos datos y código 200
Route::get('/test', function (Request $request) {
    Log::info('Test', ['user' => "???"]); //Lanzar mensaje a la consola "tail -f storage/logs/laravel.log"
    //dd("hola");
    return RespuestaAPI::exito('Funciona correctamente');
});

Route::get('/debug', function (Request $request) {
    if (config("app.debug")) { //Solo disponible cuando no está en producción
        Log::info('aaaa', ['obj' => "???"]);
        return RespuestaAPI::exito('aaa', null);
    }
    abort(404); //Si no, 404
});

//Ver los datos públicos de un usuario
Route::get('/usuario/ver/{id}', [UserController::class, 'ver'])->name('verUsuarioPublico');

//Registrarse
Route::post('/usuario', [UserController::class, 'registrar'])->name('registrarse');

//Hacer login
Route::post('/usuario/login', [UserController::class, 'login'])->name('login');

//Ver encuesta (solo si es pública)


//Buscar encuesta


//Leer datos de encuesta (solo si es público)






//RUTAS PRIVADAS que requieran un usuario logeado (bearer token):
Route::middleware('auth:sanctum')->group(function () {

    //Requieren usuario cualquiera:

    Route::get('/usuario/yo', [UserController::class, 'verYo'])->name('verUsuarioPrivado');

    Route::patch('/usuario', [UserController::class, 'editar'])->name('editarUsuario');

    //Route::delete('/usuario', [UserController::class, 'borrar'])->name('borrarUsuario');


    //Requieren usuario administrador:

    //Info para admins
    Route::get('/admin', function (Request $request) {
        return RespuestaAPI::exito("Posibles operaciones que tienes como admin", ["PATCH /usuairo/alterPerms/:uuid" => "Alterar permisos del usuario", "Otros" => "Hacer cualquier acción aunque el item no lo hayas creado tú"]);
    });

    //Requieren usuario votante:

    //Requieren usuario publicante:


});
//Validar token


//Editar usuario


//Borrar usuario


//Crear encuesta


//Editar encuesta


//Empezar encuesta


//Terminar encuesta (posible que sea tras x tiempo)


//Votar en encuesta


//Generar informe de encuesta


//Borrar encuesta


//Borrar datos de encuesta





//RUTAS DE ADMIN

//Alterar permisos de usuario





//Ruta de error 404
/*Route::get('/{x}', function (Request $request) {
    return RespuestaAPI::fallo(404, "Error 404 route not found");
});*/
