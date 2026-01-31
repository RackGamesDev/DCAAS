<?php

namespace App\Entities;

use App\Models\User;
use App\Enums\PermisosUsuario;
use Illuminate\Support\Str;
use App\Casts\PermisosUsuarioCast;

class UserEntity
{
    public string $id;
    public string $nickname;
    public string $nombre;
    public string $email;
    public ?string $descripcion = '';
    public ?string $url_foto = '';
    public PermisosUsuario $permisos;
    public ?bool $publicante = false;
    public ?string $fecha_creacion;
    public string $password;

    public static function fromModel(User $user): self
    {
        return new self($user->nickname, $user->email, $user->nombre, $user->id, $user->password, $user->descripcion, $user->url_foto, $user->publicante, PermisosUsuario::fromName($user->permisos), $user->fecha_creacion);
    }


    /**
     * Creates a User model from a UserEntity.
     */
    public function toModel(): User
    {
        $model = new User();
        $model->id = $this->id;
        $model->nickname = $this->nickname;
        $model->nombre = $this->nombre;
        $model->email = $this->email;
        $model->descripcion = $this->descripcion;
        $model->url_foto = $this->url_foto;
        $model->permisos = (new PermisosUsuarioCast())->serialize($this->permisos->value);
        $model->publicante = $this->publicante;
        $model->fecha_creacion = $this->fecha_creacion;
        $model->password = $this->password;

        return $model;
    }



    public function __construct(
        string $nickname,
        string $email,
        string $nombre,
        ?string $id = null,
        ?string $password = null,
        ?string $descripcion = null,
        ?string $url_foto = null,
        ?bool $publicante = null,
        PermisosUsuario $permisos = PermisosUsuario::Normal,
        ?string $fecha_creacion = null
    ) {
        $this->nickname = $nickname;
        $this->email = $email;
        $this->nombre = $nombre;
        $this->id = $id ?? Str::uuid();
        $this->password = $password ?? '';
        $this->descripcion = $descripcion ?? '';
        $this->url_foto = $url_foto ?? '';
        $this->publicante = $publicante ?? false;
        $this->permisos = $permisos;
        $this->fecha_creacion = $fecha_creacion ?: now();
    }

    /**
     * Summary of toArray
     * @return array{password: string, descripcion: string|null, email: string, fecha_creacion: string|null, id: string, nickname: string, nombre: string, permisos: PermisosUsuario, publicante: string|null, url_foto: string|null}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'url_foto' => $this->url_foto,
            'permisos' => $this->permisos,
            'publicante' => $this->publicante,
            'fecha_creacion' => $this->fecha_creacion,
            'password' => $this->password,
        ];
    }
}
