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
            $table->boolean('opcional')->default(false);
            $table->unsignedTinyInteger('tipo')->default(0);
            $table->foreignUuid("id_encuesta")->constrained("encuestas")->onDelete("cascade");
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
