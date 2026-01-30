<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Enums\EstadoEncuesta;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Encuesta extends Model
{
    use HasFactory;
    protected $table = 'ENCUESTAS';
    protected $fillable = [
        'nombre',
        'uuid_usuario',
        'descripcion',
        'url_foto',
        'publico',
        'votacion',
        'certificacion',
    ];

    protected $casts = [
        'estado' => EstadoEncuesta::class
    ];

    /**
     * Create an Encuesta from an Entity object.
     *
     * @param Encuesta $entity The entity object containing survey data.
     * @return self
     */
    public static function fromEntity(Encuesta $entity): self
    {
        return new self([
            'nombre' => $entity->nombre,
            'uuid_usuario' => $entity->uuid_usuario,
            'descripcion' => $entity->descripcion,
            'url_foto' => $entity->url_foto,
            'publico' => $entity->publico,
            'votacion' => $entity->votacion,
            'certificacion' => $entity->certificacion,
            'estado' => $entity->estado, //Set the state from the entity.
        ]);
    }
}
