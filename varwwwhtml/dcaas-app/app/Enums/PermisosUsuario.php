<?php

namespace App\Enums;

/**
 * Determina el nivel de permisos de un usuario
 */
enum PermisosUsuario: int {
    case Normal = 0; //Usuario regular, puede realizar acciones en la aplicacion
    case Admin = 1; //Usuario administrador, puede acceder a las funciones de administrador y sus rutas
    case Bloqueado = 2; //Usuario bloqueado, en general es como si tuviese permisos de solo lectura
    case Deshabilitado = 3; //Usuario totalmente desactivado, equivalente a borrarlo pero se almacena por cuestiones de persistencia

    public static function fromName(string $name): self
    {
        return match ($name) {
            'Normal' => self::Normal,
            'Admin' => self::Admin,
            'Bloqueado' => self::Bloqueado,
            'Deshabilitado' => self::Deshabilitado,
            default => throw new \ValueError("Invalid PermisosUsuario value: {$name}"),
        };
    }

    public static function toArray(): array {
        return [0,1,2,3];
    }
}
