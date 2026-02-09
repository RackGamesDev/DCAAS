<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\TipoPregunta;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pregunta>
 */
class PreguntaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(),
            'contenido' => fake()->sentence(),
            'descripcion' => fake()->paragraph(),
            'opcional' => fake()->boolean(),
            'tipo' => fake()->randomElement(TipoPregunta::toArray()),
            'id_encuesta' => function () {
                return \App\Models\Encuesta::factory()->create()->id;
            },
        ];
    }
}
