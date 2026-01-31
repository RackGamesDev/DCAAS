<?php

namespace App\Casts;

use Illuminate\Support\Str;
use App\Enums\PermisosUsuario;

class PermisosUsuarioCast
{
    public function serialize($value)
    {
        return $value->name;
    }

    public function deserialize($value)
    {
        return PermisosUsuario::fromName($value);
    }
}
