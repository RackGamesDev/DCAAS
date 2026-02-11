<?php

namespace App\Enums;

enum TipoPregunta: int {
    case Desarrollar = 0;
    case Check = 1;
    case Radio = 2;
    case Numero = 3;


    public static function fromName(string $name): self
    {
        return match ($name) {
            'Desarrollar' => self::Desarrollar,
            'Check' => self::Check,
            'Radio' => self::Radio,
            'Numero' => self::Numero,
            default => throw new \ValueError("Invalid TipoPregunta value: {$name}"),
        };
    }

    public static function toArray(): array {
        return [0,1,2,3];
    }
}
