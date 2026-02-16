<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear la tabla de respuestas dependiente de usuarios y encuestas
     */
    public function up(): void
    {
        Schema::create('respuestas', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique(); //El ID se guarda aparte
            $table->text('contenido')->nullable(); //Contenido de la respuesta dependiente del tipo de la pregunta
            $table->foreignUuid('id_pregunta')->constrained('preguntas')->onDelete('cascade'); //A que pregunta se responde, tiene borrado en cascada a la pregunta, o sea a la encuesta, o sea al creador de la encuesta
            $table->foreignUuid('id_user')->nullable()->constrained('users')->onDelete('set null'); //Quien responde, no es restrictivo porque no tiene borrado en cascada al usuaior
            $table->unique(['id_pregunta', 'id_user']); //Solo se permite una respuesta por usuario, ademas el usuario NO puede ni borrar ni editar su respuesta
            $table->timestamps();
        });
    }

    /**
     * Borrar la tabla de encuestas
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas');
    }
};
