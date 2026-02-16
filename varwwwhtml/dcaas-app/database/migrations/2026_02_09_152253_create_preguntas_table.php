<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear la tabla de preguntas dependiente de encuestas
     */
    public function up(): void
    {
        Schema::create('preguntas', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('titulo')->nullable(false); //Su titulo, debe de ser unico para esa encuesta
            $table->text('descripcion')->nullable(); //Descripcion de la pregunta
            $table->text('contenido')->nullable(); //Contenido en formato string (tiene un funcionamiento complejo que depende del tipo, mirar controller para mas informacion)
            $table->text('subtitulo')->nullable(); //Subtitulo similar a la descripcion
            $table->boolean('opcional')->default(false); //Si se puede no responder
            $table->unsignedTinyInteger('tipo')->default(0); //Tipo de pregunta, mirar el enum para mas informacion
            $table->text('placeholder')->nullable(); //Contenido ya marcado por defecto para guiar (depende del tipo)
            $table->text('correcta')->nullable(); //Que respuesta es la correcta (depende del tipo) TODO: que los usuarios puedan ver que tan bien han respondido
            $table->foreignUuid("id_encuesta")->constrained("encuestas")->onDelete("cascade"); //ID de la encuesta a la que pertenece, depende de esta
            $table->unique(['id_encuesta', 'titulo']);
            $table->timestamps();
        });
    }

    /**
     * Borrar la tabla de preguntas
     */
    public function down(): void
    {
        Schema::dropIfExists('preguntas');
    }
};
