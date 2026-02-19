<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de informes
     */
    public function up(): void
    {
        Schema::create('informes', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('nombre')->nullable(false)->unique(); //Nombre identificador del informe
            $table->foreignUuid('id_encuesta')->constrained('encuestas')->onDelete('cascade'); //A que encuesta hace referencia el informe, si se borra la encuesta se borran los informes
            $table->json('contenido')->nullable(false); //El contenido es un JSON
            $table->boolean('publico')->default(false); //Si es publico
            $table->string('fecha')->default(now()); //Timestamp de cuando se ha creado
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de informes
     */
    public function down(): void
    {
        Schema::dropIfExists('informes');
    }
};
