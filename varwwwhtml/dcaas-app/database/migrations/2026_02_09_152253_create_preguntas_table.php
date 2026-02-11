<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('preguntas', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('titulo')->nullable(false);
            $table->text('descripcion')->nullable();
            $table->text('contenido')->nullable();
            $table->text('subtitulo')->nullable();
            $table->boolean('opcional')->default(false);
            $table->unsignedTinyInteger('tipo')->default(0);
            $table->text('placeholder')->nullable();
            $table->text('correcta')->nullable();
            $table->foreignUuid("id_encuesta")->constrained("encuestas")->onDelete("cascade");
            $table->unique(['id_encuesta', 'titulo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preguntas');
    }
};
