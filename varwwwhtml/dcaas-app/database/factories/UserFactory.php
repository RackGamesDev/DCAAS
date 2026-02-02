<?php
namespace Database\Factories;

use App\Models\User;
use App\Enums\PermisosUsuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'nickname' => fake()->unique()->userName(),
            'nombre' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'descripcion' => fake()->sentence(),
            'url_foto' => fake()->imageUrl(200, 200, 'people'),
            'permisos' => fake()->randomElement([0, 1, 2, 3]), // Or PermisosUsuario::Normal->value
            'publicante' => fake()->boolean(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
