<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create 1 admin user manually
        User::factory()->create([
            'nickname' => 'AdminMaster',
            'email' => 'admin@example.com',
            'permisos' => 3, // Assuming 3 is Admin
        ]);

        // Create 50 random users
        User::factory()->count(500)->create();
        //$payload = User::factory(500)->make()->toArray();
        //User::insert($payload);
    }
}
