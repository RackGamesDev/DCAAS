<?php

namespace App\Enums;

/**
 * Determina el tipo de pregunta que es
 */
enum TipoPregunta: int {
    case Desarrollar = 0; //No hay opciones disponibles, en su lugar el usuario debe escribir texto para responder
    case Check = 1; //Hay ciertas opciones y solo se puede escoger una
    case Radio = 2; //Hay ciertas opciones y se pueden escoger las que sean
    case Numero = 3; //No hay opciones disponibles, en su lugar el usuario debe responder con un numero real


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
