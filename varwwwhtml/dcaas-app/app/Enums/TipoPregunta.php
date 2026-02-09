<?php

namespace App\Enums;

enum TipoPregunta: int {
    case Desarrollar = 0;
    case Check = 1;
    case Radio = 2;
    case SiNo = 3;
    case Numero = 4;


    public static function fromName(string $name): self
    {
        return match ($name) {
            'Desarrollar' => self::Desarrollar,
            'Check' => self::Check,
            'Radio' => self::Radio,
            'SiNo' => self::SiNo,
            'Numero' => self::Numero,
            default => throw new \ValueError("Invalid TipoPregunta value: {$name}"),
        };
    }

    public static function toArray(): array {
        return [0,1,2,3,4];
    }
}
