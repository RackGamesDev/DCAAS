<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstablecerPreguntasRequest;
use App\Http\Requests\VotarRequest;
use App\Models\Respuesta;
use Illuminate\Http\Request;
use App\Enums\EstadoEncuesta;
use App\Enums\TipoPregunta;
use Illuminate\Routing\Controller;
use App\Models\Encuesta;
use App\Models\Pregunta;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use App\Facades\ManejadorPermisos;

/**
 * Controller para las funciones relacionadas con las respuestas, dependiente de usuarios y preguntas
 */
class RespuestaController extends Controller
{

    public static $tamagnoPagina = 50; //El tamagno por defecto de paginacion

    /**
     * Un usuario vota en una encuesta, se espera recibir los datos de todas las respuestas para guardarlos de golpe
     * @param VotarRequest $request
     * @param mixed $id
     * @return void
     */
    public function votar(VotarRequest $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esVotante($user) || !ManejadorPermisos::puedeEditar($user))
                return RespuestaAPI::fallo(401, 'No tienes permisos para realizar esta acción');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['publico'] == false || $encuesta['estado'] != EstadoEncuesta::Activa)
                return RespuestaAPI::fallo(404, 'La encuesta no ha empezado, no es pública o no existe');
            $yaRespondido = Respuesta::join('preguntas', 'respuestas.id_pregunta', '=', 'preguntas.id')
                ->where('respuestas.id_user', $user->id)
                ->where('preguntas.id_encuesta', $id)
                ->exists();
            if ($yaRespondido)
                return RespuestaAPI::fallo(401, 'No puedes responder dos veces');
            $datos = $request->validated();
            $preguntas = Pregunta::where('id_encuesta', $encuesta->id)->orderBy('orden', 'asc')->get();
            if (!is_array($datos['respuestas']) || $preguntas->count() != count($datos['respuestas']))
                return RespuestaAPI::fallo(422, 'Debe haber las mismas respuestas que preguntas, para responder en vacio poner false');
            $preguntas = $preguntas->toArray();
            $i = 0;
            $respuestas = $datos['respuestas'];
            if (is_string($respuestas))
                $respuestas = json_decode($respuestas, true);
            ksort($respuestas);
            foreach ($respuestas as &$respuesta) { //Validacion mas especifica de las respuestas
                switch ($preguntas[$i]['tipo']) {
                    case TipoPregunta::Desarrollar->value:
                        if ($respuesta == false && $preguntas[$i]['opcional'] == true)
                            break;
                        if (($respuesta == false && $preguntas[$i]['opcional'] == false) || !is_string($respuesta))
                            return RespuestaAPI::fallo(422, 'Una pregunta obligatoria no ha sido respondida');
                        break;
                    case TipoPregunta::Check->value:
                        if ($respuesta == false && $preguntas[$i]['opcional'] == true)
                            break;
                        if (!is_array($respuesta))
                            return RespuestaAPI::fallo(422, 'La respuesta a una pregunta multiple debe de ser un array');
                        if (count($respuesta) !== count(array_unique($respuesta)))
                            return RespuestaAPI::fallo(422, 'Hay una pregunta check que tiene respuestas marcadas repetidas');
                        $opciones = explode(EstablecerPreguntasRequest::$separadorPreguntas, $preguntas[$i]['contenido']);
                        if (count($respuesta) > 0)
                            foreach ($respuesta as &$marcada)
                                if (!$marcada || !is_numeric($marcada ?? null) || (int) $marcada < 0 || (int) $marcada > count($opciones))
                                    return RespuestaAPI::fallo(422, 'Hay una pregunta check que tiene una respuesta mal formateada');
                        break;
                    case TipoPregunta::Radio->value:
                        if ($respuesta == false && $preguntas[$i]['opcional'] == true)
                            break;
                        if (!is_int($respuesta ?? null))
                            return RespuestaAPI::fallo(422, 'Hay que responder con el numero de la opcion en las preguntas check');
                        if (count(explode(EstablecerPreguntasRequest::$separadorPreguntas, $preguntas[$i]['contenido'])) < (int) $respuesta || (int) $respuesta < 0)
                            return RespuestaAPI::fallo(422, 'Respuesta fuera de rango en pregunta de check');
                        break;
                    case TipoPregunta::Numero->value:
                        if ($respuesta == false && $preguntas[$i]['opcional'] == true)
                            break;
                        if ($respuesta == false && $preguntas[$i]['opcional'] == false)
                            return RespuestaAPI::fallo(422, 'Una pregunta obligatoria no ha sido respondida');
                        if (!is_numeric($respuesta ?? null))
                            return RespuestaAPI::fallo(422, 'Las preguntas numericas hay que responderlas con numeros');
                        break;
                }
                $i++;
            }
            if ($i === count($datos['respuestas'])) {
                $i = 0;
                $insercion = [];
                //Aunque la encuesta sea anonima, se guarda el id del usuario para saber si respondio o no (pero es confidencial)
                foreach ($datos['respuestas'] as $respuesta) {
                    if (is_array($respuesta)) {
                        $insercion[] = ['contenido' => implode(EstablecerPreguntasRequest::$separadorPreguntas, $respuesta), 'id_pregunta' => $preguntas[$i]['id'], 'id_user' => $user->id, 'id' => (string) \Illuminate\Support\Str::uuid(), 'created_at' => now(), 'updated_at' => now()];
                        //Respuesta::create(['contenido' => implode(EstablecerPreguntasRequest::$separadorPreguntas, $respuesta), 'id_pregunta' => $preguntas[$i]['id'], 'id_user' => $user->id]);
                    } else {
                        $insercion[] = ['contenido' => (string) $respuesta, 'id_pregunta' => $preguntas[$i]['id'], 'id_user' => $user->id, 'id' => (string) \Illuminate\Support\Str::uuid(), 'created_at' => now(), 'updated_at' => now()];
                        //Respuesta::create(['contenido' => (string)$respuesta, 'id_pregunta' => $preguntas[$i]['id'], 'id_user' => $user->id]);
                    }
                    $i++;
                }
                Respuesta::insert($insercion);
            }
            if ($encuesta['anonimo'] == true)
                return RespuestaAPI::exito('Respuesta guardada correctamente, como la encuesta es anonima solo se guardo la respuesta, no quien responde');
            return RespuestaAPI::exito('Respuesta guardada correctamente');
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Si es el creador de la encuesta y esta no es anonima, ver las respuestas (si es anonima tambien se puede pero no se ve quien ha respondido)
     * @param Request $request
     * @param mixed $id
     * @param mixed $pagina
     * @return void
     */
    public function verRespuestasDeEncuesta(Request $request, $id, $pagina)
    {
        try {
            $pagina = (int) $pagina ?? 1;
            if (is_null($pagina) || !is_int($pagina) || $pagina < 0)
                $pagina = 1;
            $user = $request->user();
            if (!$user || !ManejadorPermisos::esPublicante($user) || !ManejadorPermisos::puedeEditar($user))
                return RespuestaAPI::fallo(401, 'No tienes permisos para realizar esta acción');
            $encuesta = Encuesta::find($id);
            if (!$encuesta || $encuesta['id_user'] != $user->id)
                return RespuestaAPI::fallo(404, 'No tienes permisos para ver estos datos sobre esa encuesta o no existe');
            if ($encuesta['estado'] != EstadoEncuesta::Terminada)
                return RespuestaAPI::fallo(401, 'No puedes ver los resultados concretos hasta que la encuesta termine (para eso primero tiene que empezar)');
            $respuestas = Respuesta::join('preguntas', 'respuestas.id_pregunta', '=', 'preguntas.id')
                ->where('preguntas.id_encuesta', $id)->select('respuestas.*')->get()->groupBy('id_pregunta')
                ->map(function ($respuestasDeEstaPregunta) {
                    return $respuestasDeEstaPregunta->map(function ($respuesta) {
                        return [
                            'id' => $respuesta->id,
                            'contenido' => $respuesta->contenido,
                            'id_pregunta' => $respuesta->id_pregunta,
                            'id_user' => $encuesta['anonimo'] == false ? $respuesta->id_user : null,
                        ];
                    });
                })->toArray();
            dd($respuestas);
        } catch (\Exception $e) {
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }

    /**
     * Sabiendo el UUID de una respuesta concreta, ver sus datos solo si el usuario es quien responde o es el creador de la encuesta y esta no es anonima (si lo es entonces no se ve el usuario que responde)
     * @param Request $request
     * @param mixed $id
     * @return void
     */
    public function verRespuestaDeEncuesta(Request $request, $id)
    {

    }



}
