<?php

namespace App\Enums;

enum Permission: string
{
    case VIEW_ANY = 'view_any';
    case VIEW = 'view';
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case RESTORE = 'restore';
    case FORCE_DELETE = 'force_delete';

    public static function all(): string
    {
        return '*';
    }

    public function for(Resource $resource): string
    {
        return $resource->value.':'.$this->value;
    }
}
