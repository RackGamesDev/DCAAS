<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

/**
 * Modelo de la encuesta dependiente del usuario
 */
class Encuesta extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['nombre', 'descripcion', 'url_foto', 'certificacion', 'votacion', 'anonimo', 'fecha_creacion', 'id_user', 'fecha_inicio', 'fecha_fin', 'publico', 'estado'];

    protected $hidden = ['publico'];

    protected $casts = [
        'id' => 'string',
        'estado' => \App\Enums\EstadoEncuesta::class,
        'publico' => 'boolean',
        'votacion' => 'boolean',
        'anonimo' => 'boolean',
    ];

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Definir permanencia al usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
