<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use App\Models\Pregunta;
use App\Models\User;

/**
 * Modelo de la respuesta dependiente de la pregunta y el usuario
 */
class Respuesta extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id_pregunta', 'id_user', 'id', 'contenido'];

    //protected $hidden = ['id_user'];

    protected $casts = [
        'id' => 'string',
    ];

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
