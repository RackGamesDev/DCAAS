<?php
namespace App\Entities;

use Illuminate\Support\Str;
use App\Enums\EstadoEncuesta;

class Encuesta {

    public string $uuid;
    public EstadoEncuesta $estado = EstadoEncuesta::SinIniciar;
    public string $nombre;
    public string $uuid_usuario;
    public ?string $descripcion = '';
    public ?string $url_foto = '';
    public bool $publico = true;
    public bool $votacion = false;
    public ?string $certificacion = '';
    public string $fechaCreacion = time() + '';
    public string $fechaInicio = '';
    public string $fechaFin = '';

    function __construct(string $nombre, string $uuid_usuario, ?string $descripcion = '', ?string $url_foto = '', ?bool $publico = true, ?bool $votacion = false, ?string $certificacion = '') {
        $this->uuid = Str::uuid();
        $this->estado = EstadoEncuesta::SinIniciar;
        $this->fechaCreacion = time() + '';
        $this->fechaInicio = '';
        $this->fechaFin = '';
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
