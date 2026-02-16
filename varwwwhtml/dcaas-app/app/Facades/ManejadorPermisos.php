<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Models\User;
use App\Enums\PermisosUsuario;
use Illuminate\Support\Facades\Log;

/**
 * Clase con las funciones que determinan que puede hacer un usuario
 */
class ManejadorPermisos// extends Facade
{
    /**
     * Determina si el usuario tiene permisos de administrador
     * @param User $user
     * @return bool
     */
    static function esAdmin(User $user) {
        return $user && $user['permisos'] == PermisosUsuario::Admin;
    }

    /**
     * Determina si el usuario puede hacer login
     * @param User $user
     * @return bool
     */
    static function puedeLogin(User $user) {
        return $user && $user['permisos'] != PermisosUsuario::Deshabilitado;
    }

    /**
     * Determina si el usuario puede editar sus propios objetos, incluido a si mismo (siempre puede borrarlos)
     * @param User $user
     * @return bool
     */
    static function puedeAutoeditar(User $user) {
        return $user && ($user['permisos'] == PermisosUsuario::Admin || $user['permisos'] == PermisosUsuario::Normal);
    }

    /**
     * Determina si el usuario tiene permisos para afectar de cualquier manera a la base de datos de la aplicacion
     * @param User $user
     * @return bool
     */
    static function puedeEditar(User $user) {
        return $user && ($user['permisos'] == PermisosUsuario::Admin || $user['permisos'] == PermisosUsuario::Normal);
    }

    /**
     * Determina si es un usuario con todo restringido
     * @param User $user
     * @return bool
     */
    static function todoRestringido(User $user) {
        return $user && $user['permisos'] == PermisosUsuario::Deshabilitado;
    }

    /**
     * Determina si el usuario puede publicar encuestas
     * @param User $user
     * @return bool
     */
    static function esPublicante(User $user) {
        return $user && $user['publicante'] == true;
    }

    /**
     * Determina si el usuario puede participar en encuestas
     * @param User $user
     * @return bool
     */
    static function esVotante(User $user) {
        return $user && $user['publicante'] == false;
    }
}
