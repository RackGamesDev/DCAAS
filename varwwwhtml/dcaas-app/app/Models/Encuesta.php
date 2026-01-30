<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Encuesta extends Model
{
    protected $table = 'ENCUESTAS';
    protected $fillable = ["uuid", "nombre", "descripcion", "uuid_user", "url_foto", "publico", "votacion", "certificacion", "anonimo"];

    /**
     * Identificador de la encuesta
     * @var string
     */
    //public string $uuid;

    /**
     * Nombre de la encuesta
     * @var string
     */
   //public string $nombre;

    /**
     * Identificador del usuario que la creó, es una relación
     * @var string
     */
    //public string $uuid_usuario;

    /**
     * Descripción de la encuesta
     * @var string
     */
    //public string $descripcion;

    /**
     * URL con la foto
     * @var string
     */
    //public string $url_foto;

    /**
     * Si la encuesta es pública
     * @var bool
     */
    //public bool $publico;

    /**
     * Si la encuesta está declarada como votación
     * @var bool
     */
    //public bool $votacion;

    /**
     * El estado de la encuesta (0=sin empezar, 1=activa, 2=terminada)
     * @var int
     */
    //public int $estado;

    /**
     * Código de certificación opcional
     * @var string
     */
    //public string $certificacion;

    /**
     * Si la encuesta es anónima
     * @var bool
     */
    //public bool $anonimo;

    function __construct(string $nombre, string $uuid_usuario, ?string $descripcion = '', ?string $url_foto = '', ?bool $publico = true, ?bool $votacion = false, ?string $certificacion = '') {
        $this->uuid = Str::uuid();
        $this->estado = 0;
        //PENDIENTE: aplicar validaciones de campos
        $this->nombre = $nombre;
        $this->uuid_usuario = $uuid_usuario;
        $this->descripcion = $descripcion ?? '';
        $this->url_foto = $url_foto ?? '';
        $this->publico = $publico ?? true;
        $this->votacion = $votacion ?? false;
        $this->certificacion = $certificacion ?? '';


    }
}
