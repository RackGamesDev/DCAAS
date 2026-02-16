<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Inserciones falsas
     */
    public function run(): void
    {
        //Crear 1 admin
        User::factory()->create([
            'nickname' => 'AdminMaster',
            'email' => 'admin@example.com',
            'permisos' => 3, // Assuming 3 is Admin
        ]);

        //Crear 500 usuarios
        User::factory()->count(500)->create();
        //$payload = User::factory(500)->make()->toArray();
        //User::insert($payload);
    }
}
