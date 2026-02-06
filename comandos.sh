#!/bin/bash
#http://localhost:8081/

#conectar al contenedor
docker exec -it apache-http bash

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
#php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
#npm install

#iniciar laravel
#con apache se sirve la carpeta public

#inicializar rutas /api
php artisan install:api
php artisan route:clear
php artisan route:list #ver lista de rutas para documentacion

#inicializar sistema de archivos
php artisan storage:link

#reestablecer (comandos tipicos para cache y arreglos)
#php artisan migrate:fresh
php artisan migrate
composer dump-autoload
php artisan config:cache
php artisan route:cache
php artisan optimize
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan sanctum:prune-expired
composer audit

#conectar a bases de datos sql o redis
#mirar .env.example

#instalar telescope para debugging
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
#http://localhost:8081/telescope

#migrar
php artisan make:migration create_encuestas_table #crea el archivo de migracion para dicha tarea (es aqui donde se definen los campos de las entidades en la app)
php artisan migrate #aplica las migraciones a la base de datos
#php artisan migrate:rollback #devuelve la base de datos al estado anterior
php artisan migrate:status
#php artisan migrate:fresh #borrar todo
#php artisan migrate:reset down() a todas las migraciones
php artisan schema:dump #fusiona los archivos de migracion existentes

#crear modelo para integrarlo a las migraciones, la app y la base de datos (un modelo permite instanciar una entidad pero no incluye sus propiedades, sirve como configuración para el orm)
php artisan make:model Encuesta

#crear factory (para definir como se crea una instancia falsa)
php artisan make:factory EncuestaFactory

#crear seeder (para mandar a poblar la base de datos usando el factory)
php artisan make:seeder UserSeeder
php artisan migrate:fresh --seed #aplicar migraciones usando seeders para poblar las bases de datos
php artisan db:seed --class=UserSeeder #aplicar solo el seeder

#instalar paquete
composer require spatie/laravel-permission

#debug
composer require spatie/laravel-tail --dev
tail -f storage/logs/laravel.log #abre una consola interactiva para debugging
#lanzar mensaje
#use Illuminate\Support\Facades\Log;
#Log::info('Checking the user data', ['user' => $user]); //Genera un log
#Log::channel('debug')->info('Checking the user data', ['user' => $user]); //Genera un log en el canal de debug
docker exec -it -w /var/www/html/dcaas-app apache-http tail -f storage/logs/laravel.log ; clear #Ver los logs en modo acoplado
docker exec -it -w /var/www/html/dcaas-app apache-http tail -f storage/logs/debug.log ; clear #Ver los logs en modo acoplado pero de debug

#crear middleware para una ruta, un middleware hace como segurata entre una ruta y la petición, haciendo que se requiera seguridad extra
php artisan make:middleware SuppressLaravel404

#crear controlador para poner lógica (acoplable a rutas)
php artisan make:controller EncuestaControlador

#crear un request para validar datos de peticiones
php artisan make:request UserRequest

#mandar a produccion (obviando comandos anteriores)
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
#APP_DEBUG=false APP_ENV=production en .env
#php artisan serve

#Hay otros objetos que no se crean con php artisan make: en uso en esta app, y otros que no se explican y sí se pueden crear con dicho comando

