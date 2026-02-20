<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use App\Models\Encuesta;

/**
 * Clase que representa un informe de una encuesta y su contenido
 */
class Informe extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'nombre', 'id_encuesta', 'contenido', 'publico', 'fecha', 'cantidad_votados'];


    protected $casts = [
        'id' => 'string',
        'contenido' => 'array',
        'publico' => 'boolean'
    ];

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Definir la pertenencia a la encuesta
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Encuesta, Informe>
     */
    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class);
    }
}
