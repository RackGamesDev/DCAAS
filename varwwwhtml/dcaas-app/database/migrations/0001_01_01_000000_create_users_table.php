<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PermisosUsuario;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //Crear la tabla de usuarios
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique(); //UUID identificativo
            $table->string('nickname')->unique()->nullable(false); //Nickname identificativo
            $table->string('nombre')->nullable(false); //Nombre normal
            $table->string('email')->unique(); //Correo identificativo
            $table->text('descripcion')->nullable(); //Descripción opcional
            $table->string('url_foto')->nullable(); //URL de la foto opcional
            //$table->enum('permisos', PermisosUsuario::toArray())->default(0); //Nivel de permisos, asignado al enum PermisosUsuario
            $table->unsignedTinyInteger('permisos')->default(0);
            //$table->unsignedTinyInteger('permisos')->default(0); //Nivel de permisos, asignado al enum PermisosUsuario
            $table->boolean('publicante')->default(false); //false = votante, true = publicante
            $table->string('fecha_creacion')->default(now()); //Fecha en la que se crea
            $table->string('password')->nullable(false); //Contrasegna hasheada
            $table->rememberToken();
            $table->timestamps();
        });

        //Tabla de sesiones
        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user_id')->index()->constrained('users'); // Changed to uuid
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
            $table->timestamp('expiration_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
