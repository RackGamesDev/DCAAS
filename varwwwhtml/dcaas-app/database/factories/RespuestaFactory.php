<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Respuesta>
 */
class RespuestaFactory extends Factory
{
    /**
     * Devuelve una respuesta falsa a alguna pregunta de algun usuario
     * @return array{contenido: string, id_pregunta: callable, id_user: callable}
     */
    public function definition(): array
    {
        return [
            'contenido' => fake()->sentence(),
            'id_pregunta' => function () {
                return \App\Models\Pregunta::factory()->create()->id;
            },
            'id_user' => function () {
                return \App\Models\User::factory()->create()->id;
            },
        ];
    }
}
