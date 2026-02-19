<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Informe;

class InformeSeeder extends Seeder
{
    /**
     * Inserciones falsas, crear 20 informes
     */
    public function run(): void
    {
        Informe::factory()->count(200)->create();
    }
}
