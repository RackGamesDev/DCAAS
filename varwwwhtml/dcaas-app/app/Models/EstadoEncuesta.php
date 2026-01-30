<?php

namespace App\Models;

enum EstadoEncuesta {
    case SinIniciar;
    case Activa;
    case Terminada;
}
