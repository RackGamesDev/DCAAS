<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Encuesta;

class EncuestaSeeder extends Seeder
{
    /**
     * Inserciones falsas, crear 200 encuestas
     */
    public function run(): void
    {
        Encuesta::factory()->count(200)->create();
        //$payload = Encuesta::factory(200)->make()->toArray();
        //Encuesta::insert($payload);
    }
}
