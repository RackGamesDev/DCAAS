<?php

namespace App\Enums;

/**
 * Hace referencia al estado actual de una encuesta
 */
enum EstadoEncuesta: int {
    case SinIniciar = 0; //Sin iniciar, se puede editar pero no se puede participar
    case Activa = 1; //Iniciada, no se puede editar pero se puede participar
    case Terminada = 2; //Ya terminada, no se puede ni editar ni participar, pero se pueden hacer informes

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
