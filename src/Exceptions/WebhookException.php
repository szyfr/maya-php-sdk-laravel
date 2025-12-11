<?php

declare(strict_types=1);

namespace Szyfr\Maya\Exceptions;

class WebhookException extends MayaException
{
    public static function invalidSignature(): self
    {
        return new self('Invalid webhook signature', 400);
    }

    public static function missingSignature(): self
    {
        return new self('Missing webhook signature header', 400);
    }
}
