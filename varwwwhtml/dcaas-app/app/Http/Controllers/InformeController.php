<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\EstadoEncuesta;
use App\Enums\PermisosUsuario;
use App\Http\Requests\CreacionInformeRequest;
use Illuminate\Routing\Controller;
use App\Models\Pregunta;
use App\Models\Encuesta;
use App\Models\Respuesta;
use App\Responses\RespuestaAPI;
use Illuminate\Support\Facades\Log;
use App\Facades\ManejadorPermisos;
use App\Enums\TipoPregunta;

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
            if (Encuesta::where('id', $id)->count() >= self::$maxInformes)
                return RespuestaAPI::fallo(403, 'No puedes hacer mas de ' . self::$maxInformes . ' informes por encuesta');
            $datos = $request->validated();

            //TODO: de momento los informes son muy basicos, de momento solo se puede ver el porcentaje de gente que pulso x opcion en preguntas de unica o multiple opcion, y ver la media de las respuestas de las preguntas de tipo numerico
            //Por lo tanto no se usa $datos ni se pide nada especifico en CreacionInformeRequest, es aqui donde se peidiria la configuracion de generacion del informe
            //(Realmente si se usa para elegir cosas como el nombre y demas, pero lo interesante seria que se usase tambien para las opciones)

            $preguntas = Pregunta::where('id_encuesta', $encuesta->id)->get()->toArray();
            if (count($preguntas) == 0)
                return RespuestaAPI::fallo(403, 'No se ha podido generar el informe porque no ha votado nadie');
            //$informe = unserialize(serialize($preguntas));
            //dd($informe);
            $informe = [];
            $i = 0;
            foreach ($preguntas as $pregunta) {
                $informe[] = ['id_pregunta' => $pregunta['id'], 'titulo' => $pregunta['titulo'], 'tipo' => $pregunta['tipo'], 'correcta' => $pregunta['correcta']];
                switch ($pregunta['tipo']) {
                    case TipoPregunta::Desarrollar->value: //Nada, por determinar
                        $informe[$i]['informacion'] = 'Esta pregunta era tipo texto, de momento no hay ninguna funcionalidad para eso';
                        break;
                    case TipoPregunta::Check->value: //Porcentaje de marcado en cada opcion
                        $respuestas = Respuesta::where('id_pregunta', $pregunta['id'])->get();
                        //dump($respuestas);
                        break;
                    case TipoPregunta::Radio->value: //Porcentaje de marcado en cada opcion
                        $respuestas = Respuesta::where('id_pregunta', $pregunta['id'])->get();

                        //dump($respuestas);
                        break;
                    case TipoPregunta::Numero->value: //Media de respuestas



                        //RESPUESTAS MAL ORDENADAS, OPCIONAL NO SE GUARDA, TODO MAL


                        $respuestas = Respuesta::where('id_pregunta', $pregunta['id'])->get();
                        dd($respuestas);
                        dump($respuestas->avg());
                        dump($respuestas->count());
                        dump($respuestas->toArray());
                        break;

                }
                $i++;
            }
            dd($informe);

            return RespuestaAPI::exito('Informe creado con exito', ['informe_id' => '']);

        } catch (\Exception $e) {
            dd($e);
            return RespuestaAPI::falloInterno(['info' => $e]);
        }
    }


    public function borrarInforme(Request $request, $id)
    {

    }

    public function verInforme(Request $request, $id)
    {

    }

    public function verInformesDeEncuesta(Request $request, $id)
    {

    }

    public function publicarInforme(Request $request, $id)
    {

    }

}
