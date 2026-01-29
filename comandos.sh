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

#iniciar laravel
#con apache se sirve la carpeta public

#inicializar rutas /api
php artisan install:api
php artisan route:clear

#reestablecer (comandos tipicos para cache y arreglos)
php artisan migrate
composer dump-autoload
php artisan config:cache
php artisan route:cache
php artisan optimize
php artisan optimize:clear

#migrar
php artisan make:migration create_encuestas_table #crea el archivo de migracion para dicha tarea
php artisan migrate #aplica las migraciones a la base de datos
#php artisan migrate:rollback #devuelve la base de datos al estado anterior
php artisan migrate:status
#php artisan migrate:fresh #borrar todo

#instalacion base


#instalar paquete


#conectar con la db


