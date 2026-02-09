<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Encuesta>
 */
class EncuestaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->sentence(),
            'descripcion' => fake()->paragraph(),
            'url_foto' => fake()->optional()->imageUrl(800, 600),
            'certificacion' => fake()->optional()->word,
            'publico' => fake()->boolean(),
            'votacion' => fake()->boolean(),
            'anonimo' => fake()->boolean(),
            'estado' => fake()->randomElement([0, 1, 2, 3]),
            'id_usuario' => function () {
                return \App\Models\User::factory()->create()->id;
            },
        ];
    }
}
