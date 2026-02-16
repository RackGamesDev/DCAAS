<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pregunta;

class PreguntaSeeder extends Seeder
{
    /**
     * Inserciones falsas, crear 700 preguntas
     */
    public function run(): void
    {
        Pregunta::factory()->count(700)->create();
        //$payload = Pregunta::factory(700)->make()->toArray();
        //Pregunta::insert($payload);
    }
}
