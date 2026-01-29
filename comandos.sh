#!/bin/bash
#http://localhost:8081/

#instalar laravel
composer global require laravel/installer

#iniciar proyecto
laravel new dcaas-app
composer create-project laravel/laravel dcaas-app
cd dcaas-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer dump-autoload
#npm install

#iniciar laravel
#con apache se sirve la carpeta public

#inicializar rutas /api
php artisan install:api
php artisan route:clear

#inicializar sistema de archivos
php artisan storage:link

#reestablecer (comandos tipicos para cache y arreglos)
php artisan migrate
composer dump-autoload
php artisan config:cache
php artisan route:cache
php artisan optimize
php artisan optimize:clear

#conectar a bases de datos sql o redis
#mirar .env.example

#instalar telescope para debugging
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

#migrar
php artisan make:migration create_encuestas_table #crea el archivo de migracion para dicha tarea
php artisan migrate #aplica las migraciones a la base de datos
#php artisan migrate:rollback #devuelve la base de datos al estado anterior
php artisan migrate:status
#php artisan migrate:fresh #borrar todo
#php artisan migrate:reset down() a todas las migraciones
php artisan schema:dump #fusiona los archivos de migracion existentes

#instalar paquete
composer require spatie/laravel-permission

#debug
composer require spatie/laravel-tail --dev
tail -f storage/logs/laravel.log #abre una consola interactiva para debugging
#lanzar mensaje
#use Illuminate\Support\Facades\Log;
#Log::info('Checking the user data', ['user' => $user]);

#mandar a produccion (obviando comandos anteriores)
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
#APP_DEBUG=false APP_ENV=production en .env
#php artisan serve

