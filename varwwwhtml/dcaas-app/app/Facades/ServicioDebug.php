<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Una clase sencilla para pruebas
 */
class ServicioDebug extends Facade
{
    protected static function get()
    {
        return 'prueba';
    }
}
