<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\RespuestaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Responses\RespuestaAPI;
use App\Http\Controllers\UserController;

/**
 * Para mas informacion sobre las rutas consultar la documentacion de la API (REST)
 */

//Este archivo es api.php, todas las rutas empezarán por /api/v1
Route::prefix('v1')->group(function () {


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
    Route::get('/encuesta/buscar/{busqueda}/{pagina?}', [EncuestaController::class, 'buscar'])->name('buscarEncuestaPublica');

    //Leer preguntas de encuesta (solo si es público)
    Route::get('/preguntas/ver/{busqueda}/{pagina?}', [PreguntaController::class, 'verDeEncuesta'])->name('verDeEncuesta');

    //TODO: ver encuestas de usuario



    //RUTAS PRIVADAS que requieran un usuario logeado (bearer token):
    Route::middleware('auth:sanctum')->group(function () {

        //Ver los datos de este usuario
        Route::get('/usuario/yo', [UserController::class, 'verYo'])->name('verUsuarioPrivado');

        //Saber si el token de sesion proporcionado es valido
        Route::get('/validarSesion', [UserController::class, 'verYo'])->name('validarSesion');

        //Borrar este usuario (provocara muchos borrados en cascada)
        Route::delete('/usuario', [UserController::class, 'borrar'])->name('borrarUsuario');

        //Cerrar la sesion actual, esto borraria el token de sesion
        Route::delete('/usuario/cerrarSesion', [UserController::class, 'cerrarSesion'])->name('cerrarSesion');

        //Requieren un usuario con permisos de edición (que puedan editar y logear):
        Route::middleware('editor')->group(function () {

            //Editar los datos de este usuario
            Route::patch('/usuario', [UserController::class, 'editar'])->name('editarUsuario');

            //Requieren usuario votante:
            Route::middleware('votante')->group(function () {

                //Envia las respuestas a una encuesta
                Route::post('/encuesta/votar/{id}', [RespuestaController::class, 'votar'])->name('votar');

                //TODO: ver si se ha votado a x encuesta

            });

            //Requieren usuario publicante:
            Route::middleware('publicante')->group(function () {

                //Crear una encuesta
                Route::post('/encuesta', [EncuestaController::class, 'crear'])->name('crearEncuesta');

                //Ver los datos de una encuesta de la cual se es propietario, incluso si es privada
                Route::get('/encuesta/verMia/{id}', [EncuestaController::class, 'verPrivado'])->name('verEncuestaPrivada');

                //Borrar una encuesta si es de ese usuario
                Route::delete('/encuesta/borrar/{id}', [EncuestaController::class, 'borrar'])->name('borrarEncuesta');

                //Editar una encuesta si es de ese usuario y no ha empezado
                Route::patch('/encuesta/editar/{id}', [EncuestaController::class, 'editar'])->name('editarEncuesta');

                //Iniciar encuesta si no ha empezado aun
                Route::post('/encuesta/iniciar/{id}', [EncuestaController::class, 'iniciar'])->name('iniciarEncuesta');

                //Finalizar encuesta si estaba activa
                Route::post('/encuesta/finalizar/{id}', [EncuestaController::class, 'finalizar'])->name('finalizarEncuesta');

                //Establecer (reemplazando o no) las preguntas de una encuesta
                Route::put('/preguntas/establecer/{id}', [PreguntaController::class, 'establecer'])->name('establecer');

                //Ver las preguntas de una encuesta que pertenezca al usuario aunque sea privada
                Route::get('/preguntas/verMia/{id}/{pagina?}', [PreguntaController::class, 'verDeEncuestaPrivado'])->name('verDeEncuestaPrivado');

                //Ver los datos disponibles de las respuestas de una encuesta terminada, que pertenezca al usuario
                Route::get('/respuestas/ver/{id}/{pagina?}', [RespuestaController::class, 'verRespuestasDeEncuesta'])->name('verRespuestasDeEncuesta');

                //Ver los datos de una respuesta concreta, si se puede
                Route::get('/respuesta/ver/{id}', [RespuestaController::class, 'verRespuestaDeEncuesta'])->name('verRespuestaDeEncuesta');

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

            //Alterar los permisos de otro usuario
            Route::patch('/admin/usuario/alterPerms', [AdminController::class, 'editarPermisos'])->name('editarPermisos');

            //Editar ciertos datos de otro usuario
            Route::patch('/admin/usuario/{id}/editar', [AdminController::class, 'editarUsuarioAjeno'])->name('editarUsuarioAjeno');

            //Borrar otro usuario, lo que provocara borrados en cascada
            Route::delete('/admin/usuario/{id}/borrar', [AdminController::class, 'borrarUsuarioAjeno'])->name('borrarUsuarioAjeno');

            //Ver todos los datos (incluso los privados) de otro usuario
            Route::get('/admin/usuario/{id}/ver', [AdminController::class, 'verUsuarioAjeno'])->name('verUsuarioAjeno');

            //Ver todo de cualquier encuesta
            Route::get('/admin/encuesta/{id}/ver', [AdminController::class, 'verEncuestaAjena'])->name('verEncuestaAjena');

            //Editar cualquier encuesta que no haya empezado
            Route::patch('/admin/encuesta/{id}/editar', [AdminController::class, 'editarEncuestaAjena'])->name('editarEncuestaAjena');

            //Borrar una encuesta ajena
            Route::delete('/admin/encuesta/{id}/borrar', [AdminController::class, 'borrarEncuestaAjena'])->name('borrarEncuestaAjena');

            //Ver todas las preguntas de una encuesta ajena aunque sea privada
            Route::get('/admin/preguntas/{id}/ver/{pagina?}', [AdminController::class, 'verPreguntasEncuestaAjena'])->name('verPreguntasEncuestaAjena');


        });




    });

    //Ruta de error 404
/*Route::get('/{x}', function (Request $request) {
    return RespuestaAPI::fallo(404, 'Error 404 route not found');
});*/

});
