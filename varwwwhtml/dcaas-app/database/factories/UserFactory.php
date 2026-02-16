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

    /**
     * Devuelve un usuario falso
     * @return array{descripcion: string, email: string, id: string, nickname: string, nombre: string, password: string, permisos: mixed, publicante: bool, remember_token: string, url_foto: string}
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'nickname' => fake()->unique()->userName(),
            'nombre' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'descripcion' => fake()->sentence(),
            'url_foto' => fake()->imageUrl(200, 200, 'people'),
            'permisos' => fake()->randomElement(PermisosUsuario::toArray()),
            'publicante' => fake()->boolean(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
