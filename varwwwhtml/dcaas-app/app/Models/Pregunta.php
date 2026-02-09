<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use App\Models\Encuesta;

class Pregunta extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'descripcion', 'titulo', 'contenido', 'opcional', 'tipo', 'id_encuesta'];

    //protected $hidden = [''];

    protected $casts = [
        'id' => 'string',
        'tipo' => \App\Enums\TipoPregunta::class,
        'opcional' => 'boolean',
    ];

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }

    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class);
    }
}
