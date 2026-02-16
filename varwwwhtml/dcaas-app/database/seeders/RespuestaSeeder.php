<?php

namespace Database\Seeders;

use App\Models\Respuesta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RespuestaSeeder extends Seeder
{
    /**
     * Inserciones falsas, crear 500 respuestas
     */
    public function run(): void
    {
        Respuesta::factory()->count(500)->create();
    }
}
