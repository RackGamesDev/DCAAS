<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Models\User;
use App\Enums\PermisosUsuario;

class ManejadorPermisos extends Facade
{
    protected static function esAdmin(User $user) {
        return $user && is_numeric($user['permisos']) && $user['permisos'] == PermisosUsuario::Admin;
    }

    protected static function puedeLogin(User $user) {
        return $user && is_numeric($user['permisos']) && $user['permisos'] != PermisosUsuario::Deshabilitado;
    }

    protected static function puedeAutoeditar(User $user) {
        return $user && is_numeric($user['permisos']) && ($user['permisos'] == PermisosUsuario::Admin || $user['permisos'] == PermisosUsuario::Normal);
    }

    protected static function puedeEditar(User $user) {
        return $user && is_numeric($user['permisos']) && ($user['permisos'] == PermisosUsuario::Admin || $user['permisos'] == PermisosUsuario::Normal);
    }

    protected static function todoRestringido(User $user) {
        return $user && is_numeric($user['permisos']) && $user['permisos'] == PermisosUsuario::Deshabilitado;
    }

    protected static function esPublicante(User $user) {
        return $user && $user['publicante'] == true;
    }
}
