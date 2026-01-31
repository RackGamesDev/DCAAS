<?php

namespace App\Enums;

enum EstadoEncuesta: int {
    case SinIniciar = 0;
    case Activa = 1;
    case Terminada = 2;
}
