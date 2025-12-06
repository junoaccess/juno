<?php

namespace App\Exceptions;

use App\Models\Invitation;
use RuntimeException;

class InvitationException extends RuntimeException
{
    public static function cannotBeAccepted(Invitation $invitation): self
    {
        $reason = $invitation->is_expired
            ? 'This invitation has expired.'
            : 'This invitation has already been accepted or is no longer valid.';

        return new self($reason);
    }

    public static function notFound(): self
    {
        return new self('The invitation could not be found.');
    }

    public static function alreadyAccepted(): self
    {
        return new self('This invitation has already been accepted.');
    }
}
