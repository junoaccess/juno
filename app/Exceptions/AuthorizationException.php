<?php

namespace App\Exceptions;

use RuntimeException;

class AuthorizationException extends RuntimeException
{
    public static function insufficientPermissions(): self
    {
        return new self('You do not have permission to perform this action.');
    }

    public static function notMemberOfOrganisation(): self
    {
        return new self('You do not have access to this organisation.');
    }
}
