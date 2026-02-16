<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Encuesta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\EncuestaSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Inserciones falsas, llamar a todos los seeders
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/
        $this->call([
            UserSeeder::class,
            EncuestaSeeder::class,
            PreguntaSeeder::class,
        ]);
    }
}
