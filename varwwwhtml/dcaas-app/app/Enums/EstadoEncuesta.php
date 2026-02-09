<?php

namespace App\Enums;

enum EstadoEncuesta: int {
    case SinIniciar = 0;
    case Activa = 1;
    case Terminada = 2;

    public static function fromName(string $name): self
    {
        return match ($name) {
            'SinIniciar' => self::SinIniciar,
            'Activa' => self::Activa,
            'Terminada' => self::Terminada,
            default => throw new \ValueError("Invalid EstadoEncuesta value: {$name}"),
        };
    }

    public static function toArray(): array {
        return [0,1,2];
    }
}
