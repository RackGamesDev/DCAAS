<?php

namespace App\Enums;

enum PermisosUsuario: int {
    case Normal = 0;
    case Admin = 1;
    case Bloqueado = 2;
    case Deshabilitado = 3;

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
