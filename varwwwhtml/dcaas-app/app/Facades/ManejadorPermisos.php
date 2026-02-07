<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Models\User;
use App\Enums\PermisosUsuario;
use Illuminate\Support\Facades\Log;

class ManejadorPermisos// extends Facade
{
    static function esAdmin(User $user) {
        return $user && $user['permisos'] == PermisosUsuario::Admin;
    }

    static function puedeLogin(User $user) {
        return $user && $user['permisos'] != PermisosUsuario::Deshabilitado;
    }

    static function puedeAutoeditar(User $user) {
        return $user && ($user['permisos'] == PermisosUsuario::Admin || $user['permisos'] == PermisosUsuario::Normal);
    }

    static function puedeEditar(User $user) {
        return $user && ($user['permisos'] == PermisosUsuario::Admin || $user['permisos'] == PermisosUsuario::Normal);
    }

    static function todoRestringido(User $user) {
        return $user && $user['permisos'] == PermisosUsuario::Deshabilitado;
    }

    static function esPublicante(User $user) {
        return $user && $user['publicante'] == true;
    }

    static function esVotante(User $user) {
        return $user && $user['publicante'] == false;
    }
}
