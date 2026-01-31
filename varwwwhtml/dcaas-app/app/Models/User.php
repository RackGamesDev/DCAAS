<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\PermisosUsuarioCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

//Modelo del usuario que define como se comportará con el ORM Eloquent
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Atributos asignables en masa
     *
     * @var list<string>
     */
    protected $fillable = [
        'nickname',
        'nombre',
        'email',
        'descripcion',
        'url_foto',
        'permisos',
        'publicante',
        'fecha_creacion',
        'password',
    ];

    /**
     * Atributos ocultos (por ejemplo, ante respuestas de la API)
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Hace que permisos, a pesar de ser un enum PermisosUsuario, se guarde como int (o que al reves, cuando se reciba de la base de datos se represente como el enum)
     *
     * @var array
     */
    protected $casts = [
        //'permisos' => PermisosUsuarioCast::class,
        'permisos' => \App\Enums\PermisosUsuario::class,
    ];

    /**
     * Encripta la contrasegna antes de guardarla (para guardar la encriptada)
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Devuelve los atributos a los que hay que hacer cast.
     *
     * @return array<string, string>
     */
    /*protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'permisos' => 'integer'
        ];
    }*/
}
