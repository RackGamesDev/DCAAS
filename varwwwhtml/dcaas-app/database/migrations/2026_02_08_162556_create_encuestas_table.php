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
        Schema::create('encuestas', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('nombre')->nullable(false)->unique();
            $table->text('descripcion')->nullable();
            $table->string('url_foto')->nullable();
            $table->string('certificacion')->nullable();
            $table->boolean('publico')->default(true);
            $table->boolean('votacion')->default(false);
            $table->boolean('anonimo')->default(false);
            $table->string('fecha_creacion')->default(now());
            $table->string('fecha_inicio')->default("N/A");
            $table->string('fecha_fin')->default("N/A");
            $table->unsignedTinyInteger('estado')->default(0);
            $table->foreignUuid("id_user")->constrained("users")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};
