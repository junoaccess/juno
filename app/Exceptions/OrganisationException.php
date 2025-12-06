<?php

namespace App\Exceptions;

use App\Models\Organization;
use RuntimeException;

class OrganisationException extends RuntimeException
{
    public static function notFound(): self
    {
        return new self('The organisation could not be found.');
    }

    public static function userNotMember(Organization $organisation): self
    {
        return new self("You are not a member of {$organisation->name}.");
    }

    public static function cannotDelete(Organization $organisation): self
    {
        return new self("The organisation '{$organisation->name}' cannot be deleted.");
    }
}
