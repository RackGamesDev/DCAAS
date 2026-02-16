<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\EstadoEncuesta;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Encuesta>
 */
class EncuestaFactory extends Factory
{

    /**
     * Devuelve una encuesta falsa de algun usuario
     * @return array{anonimo: bool, certificacion: string, descripcion: string, estado: mixed, id_user: callable, nombre: string, publico: bool, url_foto: string, votacion: bool}
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
            'estado' => fake()->randomElement(EstadoEncuesta::toArray()),
            'id_user' => function () {
                return \App\Models\User::factory()->create()->id;
            },
        ];
    }
}
