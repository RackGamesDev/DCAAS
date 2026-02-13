<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pregunta;

class PreguntaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pregunta::factory()->count(700)->create();
        //$payload = Pregunta::factory(700)->make()->toArray();
        //Pregunta::insert($payload);
    }
}
