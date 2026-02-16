<?php

namespace App\Observers;

use App\Models\Respuesta;

class RespuestaObserver
{
    /**
     * Handle the Respuesta "created" event.
     */
    public function created(Respuesta $respuesta): void
    {
        //
    }

    /**
     * Handle the Respuesta "updated" event.
     */
    public function updated(Respuesta $respuesta): void
    {
        //
    }

    /**
     * Handle the Respuesta "deleted" event.
     */
    public function deleted(Respuesta $respuesta): void
    {
        //
    }

    /**
     * Handle the Respuesta "restored" event.
     */
    public function restored(Respuesta $respuesta): void
    {
        //
    }

    /**
     * Handle the Respuesta "force deleted" event.
     */
    public function forceDeleted(Respuesta $respuesta): void
    {
        //
    }
}
