<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\PreguntaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Responses\RespuestaAPI;
use App\Http\Controllers\UserController;

Route::prefix('v1')->group(function () {
    //Este archivo es api.php, todas las rutas empezarán por /api

    //RUTAS PÚBLICAS:

    //Ruta de ejemplo, ante GET /api/test manda un json con esos datos y código 200
    Route::get('/test', function (Request $request) {
        Log::info('Test', ['user' => '???']); //Lanzar mensaje a la consola 'tail -f storage/logs/laravel.log'
        //dd('hola');
        return RespuestaAPI::exito('Funciona correctamente');
    })->name('test');

    Route::get('/debug', function (Request $request) {
        if (config('app.debug')) { //Solo disponible cuando no está en producción
            Log::info('aaaa', ['obj' => '???']);
            return RespuestaAPI::exito('aaa', null);
        }
        abort(404); //Si no, 404
    })->name('debug');

    //Ver los datos públicos de un usuario
    Route::get('/usuario/ver/{id}', [UserController::class, 'ver'])->name('verUsuarioPublico');

    //Registrarse
    Route::post('/usuario', [UserController::class, 'registrar'])->name('registrarse');

    //Hacer login
    Route::post('/usuario/login', [UserController::class, 'login'])->name('login');

    //Ver encuesta (solo si es pública)
    Route::get('/encuesta/ver/{id}', [EncuestaController::class, 'verPublico'])->name('verEncuestaPublica');

    //Buscar encuesta
    Route::get('/encuesta/buscar/{busqueda}', [EncuestaController::class, 'buscar'])->name('buscarEncuestaPublica');

    //Leer preguntas de encuesta (solo si es público)
    Route::get('/preguntas/ver/{busqueda}', [EncuestaController::class, 'verDeEncuesta'])->name('verDeEncuesta');





    //RUTAS PRIVADAS que requieran un usuario logeado (bearer token):
    Route::middleware('auth:sanctum')->group(function () {

        //Requieren usuario cualquiera:

        Route::get('/usuario/yo', [UserController::class, 'verYo'])->name('verUsuarioPrivado');
        Route::get('/validarSesion', [UserController::class, 'verYo'])->name('validarSesion');

        Route::delete('/usuario', [UserController::class, 'borrar'])->name('borrarUsuario');

        Route::delete('/usuario/cerrarSesion', [UserController::class, 'cerrarSesion'])->name('cerrarSesion');

        //Requieren un usuario con permisos de edición (que puedan editar y logear):
        Route::middleware('editor')->group(function () {
            Route::patch('/usuario', [UserController::class, 'editar'])->name('editarUsuario');

            //Requieren usuario votante:
            Route::middleware('votante')->group(function () {



            });

            //Requieren usuario publicante:
            Route::middleware('publicante')->group(function () {
                Route::post("/encuesta", [EncuestaController::class, 'crear'])->name('crearEncuesta');
                Route::get('/encuesta/verMia/{id}', [EncuestaController::class, 'verPrivado'])->name('verEncuestaPrivada');
                Route::delete('/encuesta/borrar/{id}', [EncuestaController::class, 'borrar'])->name('borrarEncuesta');
                Route::patch('/encuesta/editar/{id}', [EncuestaController::class, 'editar'])->name('editarEncuesta');
                Route::post('/encuesta/iniciar/{id}', [EncuestaController::class, 'iniciar'])->name('iniciarEncuesta');
                Route::post('/encuesta/finalizar/{id}', [EncuestaController::class, 'finalizar'])->name('finalizarEncuesta');

                Route::get('/preguntas/verMia/{id}', [PreguntaController::class, 'verDeEncuestaPrivado'])->name('verDeEncuestaPrivado');
                Route::put('/preguntas/establecer/{id}', [PreguntaController::class, 'establecer'])->name('establecer');

            });

        });

        //Requieren un usuario que simplemente pueda logear (no hace falta que pueda editar):
        Route::middleware('logeable')->group(function () {


        });



        //Requieren usuario administrador:
        Route::middleware('admin')->group(function () {
            //Info para admins
            Route::get('/admin', function (Request $request) {
                return RespuestaAPI::exito('Posibles operaciones que tienes como admin', ['Otros' => 'Hacer cualquier acción aunque el item no lo hayas creado tú, en general consulta la documentación']);
            });

            Route::patch('/admin/usuario/alterPerms', [AdminController::class, 'editarPermisos'])->name('editarPermisos');

            Route::patch('/admin/usuario/{id}/editar', [AdminController::class, 'editarUsuarioAjeno'])->name('editarUsuarioAjeno');

            Route::delete('/admin/usuario/{id}/borrar', [AdminController::class, 'borrarUsuarioAjeno'])->name('borrarUsuarioAjeno');

            Route::get('/admin/usuario/{id}/ver', [AdminController::class, 'verUsuarioAjeno'])->name('verUsuarioAjeno');

            Route::get('/admin/encuesta/{id}/ver', [AdminController::class, 'verEncuestaAjena'])->name('verEncuestaAjena');

            Route::patch('/admin/encuesta/{id}/editar', [AdminController::class, 'editarEncuestaAjena'])->name('editarEncuestaAjena');

            Route::delete('/admin/encuesta/{id}/borrar', [AdminController::class, 'borrarEncuestaAjena'])->name('borrarEncuestaAjena');



        });




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
    return RespuestaAPI::fallo(404, 'Error 404 route not found');
});*/

});
