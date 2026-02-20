<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\EstadoEncuesta;
use App\Http\Requests\CreacionInformeRequest;
use Illuminate\Routing\Controller;
use App\Models\Pregunta;
use App\Models\Encuesta;
use App\Models\Respuesta;
use App\Models\Informe;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use App\Facades\ManejadorPermisos;
use App\Enums\TipoPregunta;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EstablecerPreguntasRequest;

class InformeController extends Controller
{

    public static $entregablesPublicos = ['id', 'nombre', 'contenido', 'publico', 'id_encuesta', 'fecha']; //Campos considerados publicos a la hora de entregar
    public static $maxInformes = 5; //El maximo de informes que se pueden hacer por encuesta

    /**
     * Crea un informe de una encuesta terminada en base a ciertos parametros
     * @param CreacionInformeRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function crearInforme(CreacionInformeRequest $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user) || !ManejadorPermisos::puedeEditar($user))
                return RespuestaAPI::fallo(401, 'No tienes permisos para realizar esta acción');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta['estado'] != EstadoEncuesta::Terminada)
                return RespuestaAPI::fallo(403, 'No puedes crear informes hasta que termine la encuesta');
            if (Informe::where('id_encuesta', $encuesta->id)->count() >= self::$maxInformes)
                return RespuestaAPI::fallo(403, 'No puedes hacer mas de ' . self::$maxInformes . ' informes por encuesta');
            $datos = $request->validated();

            //TODO: de momento los informes son muy basicos, de momento solo se puede ver el porcentaje de gente que pulso x opcion en preguntas de unica o multiple opcion, y ver la media de las respuestas de las preguntas de tipo numerico
            //Por lo tanto no se usa $datos ni se pide nada especifico en CreacionInformeRequest, es aqui donde se peidiria la configuracion de generacion del informe
            //(Realmente si se usa para elegir cosas como el nombre y demas, pero lo interesante seria que se usase tambien para las opciones)

            $preguntas = Pregunta::where('id_encuesta', $encuesta->id)->get()->toArray();
            if (count($preguntas) == 0)
                return RespuestaAPI::fallo(403, 'No se ha podido generar el informe porque no ha votado nadie');
            $informe = [];
            $i = 0;
            $cantidadVotados = DB::table('respuestas')->join('preguntas', 'respuestas.id_pregunta', '=', 'preguntas.id')->where('preguntas.id_encuesta', $encuesta->id)->distinct()->count('respuestas.id_user');
            foreach ($preguntas as $pregunta) {
                $informe[] = ['id_pregunta' => $pregunta['id'], 'titulo' => $pregunta['titulo'], 'tipo' => $pregunta['tipo'], 'correcta' => $pregunta['correcta']];
                switch ($pregunta['tipo']) {
                    case TipoPregunta::Desarrollar->value: //Nada, por determinar
                        if (is_string($pregunta['correcta']) && strlen($pregunta['correcta']) > 0)
                            $informe[$i]['porcentajeAciertos'] = (float) Respuesta::where('id_pregunta', $pregunta['id'])->where('contenido', $pregunta['correcta'])->count() * 100.0 / (float) $cantidadVotados;
                        if ($pregunta['opcional'] == true)
                            $informe[$i]['cantidadRespondidos'] = Respuesta::where('id_pregunta', $pregunta['id'])->whereNot('contenido', '')->whereNot('contenido', null)->count();
                        $informe[$i]['informacion'] = 'Esta pregunta era tipo texto, de momento no hay ninguna funcionalidad para eso';
                        break;
                    case TipoPregunta::Check->value: //Porcentaje de marcado en cada opcion
                        if ($pregunta['opcional'] == true)
                            $informe[$i]['cantidadRespondidos'] = Respuesta::where('id_pregunta', $pregunta['id'])->whereNot('contenido', '')->whereNot('contenido', null)->count();
                        $respuestas = Respuesta::where('id_pregunta', $pregunta['id'])->get();
                        $informe[$i]['porcentajes'] = [];
                        $opciones = explode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['contenido']);
                        $respuestasTotal = [];
                        $respuestas = Respuesta::where('id_pregunta', $pregunta['id'])->whereNot('contenido', '')->whereNot('contenido', null)->pluck('contenido');
                        foreach ($respuestas as $respuesta)
                            $respuestasTotal = array_merge($respuestasTotal, explode(EstablecerPreguntasRequest::$separadorPreguntas, $respuesta));
                        $apariciones = array_count_values($respuestasTotal);
                        $ii = 0;
                        foreach ($opciones as $opcion) {
                            $informe[$i]['porcentajes'][] = ['texto_opcion' => $opcion, 'numero' => $i, 'cantidad' => data_get($apariciones, $ii, 0), 'porcentaje' => ((float) data_get($apariciones, $ii, 0)) * 100.0 / (float) $cantidadVotados];
                            $ii++;
                        }
                        if (strlen($pregunta['correcta']) > 0) {
                            $totalBien = 0;
                            $correctas = explode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['correcta']);
                            foreach ($correctas as $correcta)
                                $totalBien += (int) data_get($apariciones, $correcta, 0);
                            $informe[$i]['porcentajeAciertos'] = (float) $totalBien / 100.0 * (float) $cantidadVotados; //Se calcula de manera restrictiva
                        }
                        break;
                    case TipoPregunta::Radio->value: //Porcentaje de marcado en cada opcion
                        if (strlen($pregunta['correcta']) > 0)
                            $informe[$i]['porcentajeAciertos'] = (float) Respuesta::where('id_pregunta', $pregunta['id'])->where('contenido', $pregunta['correcta'])->count() * 100.0 / (float) $cantidadVotados;
                        if ($pregunta['opcional'] == true)
                            $informe[$i]['cantidadRespondidos'] = Respuesta::where('id_pregunta', $pregunta['id'])->whereNot('contenido', '')->whereNot('contenido', null)->count();
                        $informe[$i]['porcentajes'] = [];
                        $opciones = explode(EstablecerPreguntasRequest::$separadorPreguntas, $pregunta['contenido']);
                        $ii = 0;
                        foreach ($opciones as $opcion) {
                            $cantidadCruda = Respuesta::where('id_pregunta', $pregunta['id'])->where('contenido', (string) $ii)->count();
                            $informe[$i]['porcentajes'][] = ['texto_opcion' => $opcion, 'porcentaje' => (float) $cantidadCruda * 100.0 / (float) $cantidadVotados, 'cantidad' => $cantidadCruda, 'numero' => $ii];
                            $ii++;
                        }
                        break;
                    case TipoPregunta::Numero->value: //Media de respuestas
                        if (is_numeric($pregunta['correcta']))
                            $informe[$i]['porcentajeAciertos'] = (float) Respuesta::where('id_pregunta', $pregunta['id'])->where('contenido', $pregunta['correcta'])->count() * 100.0 / (float) $cantidadVotados;
                        if ($pregunta['opcional'] == true)
                            $informe[$i]['cantidadRespondidos'] = Respuesta::where('id_pregunta', $pregunta['id'])->whereNot('contenido', '')->whereNot('contenido', null)->count();
                        $respuestas = Respuesta::where('id_pregunta', $pregunta['id'])->pluck('contenido')->map(fn($v) => trim($v))
                            ->filter(fn($v) => is_numeric($v))
                            ->map(fn($v) => (float) $v)->avg();
                        $informe[$i]['media'] = $respuestas;
                        break;

                }
                $i++;
            }
            $informe = array_reverse($informe);
            $datos['cantidad_votados'] = $cantidadVotados;
            $datos['contenido'] = $informe;
            $datos['id_encuesta'] = $encuesta->id;
            unset($datos['opciones']);
            $id = Informe::create($datos);
            return RespuestaAPI::exito('Informe creado con exito', ['informe_id' => $id->id]);

        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Borra un informe si se es el duegno
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrarInforme(Request $request, $id)
    {
        try {
            $user = $request->user();
            $informe = Informe::find($id);
            if (!$informe || !$user)
                return RespuestaAPI::fallo(404, 'Informe no encontrado o no tienes permisos para esto');
            $encuesta = Encuesta::find($informe->id_encuesta);
            if ($user->id != $encuesta->id_user)
                return RespuestaAPI::fallo(404, 'Informe no encontrado o no tienes permisos para esto');
            $idPrevio = $informe->id;
            $informe->delete();
            return RespuestaAPI::exito('Informe borrado con exito', ['informe' => $idPrevio]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Visualizar un informe publico
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verInforme($id)
    {
        try {
            $informe = Informe::find($id);
            if (!$informe)
                return RespuestaAPI::fallo(404, 'Informe no encontrado');
            if ($informe->publico == false)
                return RespuestaAPI::fallo(404, 'Informe no encontrado');
            return RespuestaAPI::exito('Informe encontrado', ['informe' => $informe->only(self::$entregablesPublicos)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Ver los ids de los informes de una encuesta de manera publica
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verInformesDeEncuesta($id)
    {
        try {
            $encuesta = Encuesta::find($id);
            if (!$encuesta)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta->publico == false)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta->estado != EstadoEncuesta::Terminada)
                return RespuestaAPI::fallo(404, 'No se han podido buscar los informes de esta encuesta porque aun no ha terminado');
            $informes = Informe::select('id')->where('id_encuesta', $encuesta->id)->where('publico', true);
            if ($informes->count() == 0)
                return RespuestaAPI::fallo(404, 'Informes no encontrados');
            return RespuestaAPI::exito('Informes encontrados', ['informes' => $informes->get()]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Publica un informe que era privado
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicarInforme(Request $request, $id)
    {
        try {
            $user = $request->user();
            $informe = Informe::find($id);
            if (!$informe || !$user)
                return RespuestaAPI::fallo(404, 'Informe no encontrado o no tienes permisos para esto');
            $encuesta = Encuesta::find($informe->id_encuesta);
            if ($user->id != $encuesta->id_user)
                return RespuestaAPI::fallo(404, 'Informe no encontrado o no tienes permisos para esto');
            if ($informe->publico == true)
                return RespuestaAPI::fallo(403, 'El informe ya era publico');
            $informe->fill(['publico' => true]);
            $informe->save();
            return RespuestaAPI::exito('Informe publicado con exito', ['informe' => $informe->id]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Ver el informe sea privado o publico, si se es duegno
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verMiInforme(Request $request, $id) {
        try {
            $user = $request->user();
            $informe = Informe::find($id);
            if (!$informe)
                return RespuestaAPI::fallo(404, 'Informe no encontrado');
            $encuesta = Encuesta::find($informe->id_encuesta);
            if (!$user || $encuesta->id_user != $user->id)
                return RespuestaAPI::fallo(403, 'Esta ruta esta pensada para ver tus encuestas, quizas no tengas permisos');
            return RespuestaAPI::exito('Informe encontrado', ['informe' => $informe->only(self::$entregablesPublicos)]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Ver todos los informes aunque sean privados de una encuesta de la cual se es duegno
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verInformesMiEncuesta(Request $request, $id) {
        try {
            $user = $request->user();
            $encuesta = Encuesta::find($id);
            if (!$encuesta)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if (!$user || $encuesta->id_user != $user->id)
                return RespuestaAPI::fallo(404, 'Encuesta no encontrada');
            if ($encuesta->estado != EstadoEncuesta::Terminada)
                return RespuestaAPI::fallo(404, 'No se han podido buscar los informes de esta encuesta porque aun no ha terminado');
            $informes = Informe::select('id')->where('id_encuesta', $encuesta->id);
            if ($informes->count() == 0)
                return RespuestaAPI::fallo(404, 'Informes no encontrados');
            return RespuestaAPI::exito('Informes encontrados', ['informes' => $informes->get()]);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

}
