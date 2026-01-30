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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('nickname')->unique()->nullable(false);
            $table->string('nombre')->nullable(false);
            $table->string('correo')->unique();
            $table->text('descripcion')->nullable();
            $table->string('url_foto')->nullable();
            $table->enum('permisos', [0,1,2,3])->default(0);
            $table->boolean('publicante')->default(false);
            $table->string('fecha_creacion')->default(now());
            $table->string('contrasegna')->nullable(false);
            $table->rememberToken();
            $table->timestamps();
        });

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
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
