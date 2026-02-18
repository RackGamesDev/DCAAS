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
     * Devuelve una pregunta falsa de alguna encuesta
     * @return array{contenido: string, correcta: string, descripcion: string, id_encuesta: callable, opcional: bool, placeholder: string, subtitulo: string, tipo: mixed, titulo: string}
     */
    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(),
            'contenido' => fake()->sentence(),
            'placeholder' => fake()->sentence(),
            'subtitulo' => fake()->sentence(),
            'correcta' => fake()->sentence(),
            'descripcion' => fake()->paragraph(),
            'opcional' => fake()->boolean(),
            'tipo' => fake()->randomElement(TipoPregunta::toArray()),
            'orden' => fake()->randomNumber(),
            'id_encuesta' => function () {
                return \App\Models\Encuesta::factory()->create()->id;
            },
        ];
    }
}
