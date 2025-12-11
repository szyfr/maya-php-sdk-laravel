<?php

declare(strict_types=1);

namespace Szyfr\Maya\Exceptions;

class AuthenticationException extends MayaException
{
    public static function invalidCredentials(): self
    {
        return new self('Invalid Maya API credentials', 401);
    }
}
