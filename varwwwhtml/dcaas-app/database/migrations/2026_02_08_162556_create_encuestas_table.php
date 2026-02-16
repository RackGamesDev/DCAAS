<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear la tabla de encuestas dependiente de usuarios (publicantes)
     */
    public function up(): void
    {
        Schema::create('encuestas', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('nombre')->nullable(false)->unique(); //Nombre identificativo de la encuesta
            $table->text('descripcion')->nullable(); //Descripcion de la encuesta
            $table->string('url_foto')->nullable(); //URL de la foto representativa
            $table->string('certificacion')->nullable(); //Codigo opcional de certificacion para dar mas confianza
            $table->boolean('publico')->default(true); //Si la encuesta esta publicada o no, es necesario para iniciarla TODO: que las encuestas puedan estar restringidas a x usuarios
            $table->boolean('votacion')->default(false); //Si esta declarada como votacion, no afecta en nada a su funcionamiento pero puede tener un uso mas adelante
            $table->boolean('anonimo')->default(false); //Si la encuesta es anonima, osea que no se muestran nunca quien ha respondido
            $table->string('fecha_creacion')->default(now()); //Timestamp de cuando se ha creado
            $table->string('fecha_inicio')->default('N/A'); //Timestamp de cuando se ha iniciado (o vacio si no ha iniciado aun)
            $table->string('fecha_fin')->default('N/A'); //Timestamp de cuando ha finalizado (o vacio si no ha terminado nunca aun)
            $table->unsignedTinyInteger('estado')->default(0); //Estado de la encuesta, ver el enum para mas informacion
            $table->foreignUuid('id_user')->constrained('users')->onDelete('cascade'); //ID del usuario creador, debe de ser publicante y depende de este
            $table->timestamps();
        });
    }

    /**
     * Borrar la tabla de encuestas
     */
    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};
