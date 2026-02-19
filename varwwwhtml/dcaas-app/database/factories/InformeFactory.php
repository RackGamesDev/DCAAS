<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Informe>
 */
class InformeFactory extends Factory
{

    /**
     * Devuelve un informe falso de alguna encuesta
     * @return array{contenido: array{data: string, id_encuesta: callable, nombre: string, publico: bool}}
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->sentence(3),
            'id_encuesta' => function () {
                return \App\Models\Encuesta::factory()->create()->id;
            },
            'contenido' => [
                'data' => $this->faker->name,
            ],
            'publico' => $this->faker->boolean(20),
        ];
    }
}
